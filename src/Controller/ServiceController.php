<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Stock controller.
 *
 * @Route("services")
 */
class ServiceController extends Controller {
    
    public function getTitreByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('designation')) {
            $chaine .= 'Désignation contient ' . $request->query->get('designation');
            $i++;
        }
        if ($request->query->get('type')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Type : ' . $request->query->get('type');
        }
        if ($request->query->get('mouvement')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Mouvement : ' . $request->query->get('mouvement');
        }
        if ($request->query->get('client')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $client = $em->getRepository('App\\Entity\\Client')->findOneById($request->query->get('client'));
            $chaine .= 'Code client : ' . $client->getCode();
        }
        if ($request->query->get('fournisseur')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $fournisseur = $em->getRepository('App\\Entity\\Fournisseur')->findOneById($request->query->get('fournisseur'));
            $chaine .= 'Code fournisseur : ' . $fournisseur->getCode();
        }
        if ($request->query->get('article')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $article = $em->getRepository('App\\Entity\\Article')->findOneById($request->query->get('article'));
            $chaine .= 'Code article : ' . $article->getCode();
        }
        $dateFormat = $this->get('app.format_date');
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($startDateCreation) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date création >= ' . str_replace('/', '-', $request->query->get('startDateCreation'));
            }
        }
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($endDateCreation) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date création <= ' . str_replace('/', '-', $request->query->get('endDateCreation'));
            }
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getQbByParametres($em, $request) {
        $qb = $em->getRepository('App\\Entity\\Stock')->createQueryBuilder('a');
        $qb->join('a.article', 'art');
        if (!$request->query->get('type') or ( $request->query->get('type') === 'tous')) {
            $qb->where('art.service=1')
                    ->orWhere('art.service=0')->andWhere('art.stockable=0');
        } elseif ($request->query->get('type') === 'services') {
            $qb->where('art.service=1');
        } elseif ($request->query->get('type') === 'produitNonStockable') {
            $qb->where('art.service=0')->andWhere('art.stockable=0');
        }

        if ($request->query->get('designation')) {
            $qb->andWhere('a.designation like :designation')->setParameter('designation', '%' . $request->query->get('designation') . '%');
        }
        if ($request->query->get('mouvement')) {
            $mouvement = $request->query->get('mouvement');
            if ($mouvement === 'entre') {
                $qb->andWhere('a.mouvement = true');
                if ($request->query->get('fournisseur')) {
                    $qb->andWhere('a.fournisseur = :fournisseur')->setParameter('fournisseur', $request->query->get('fournisseur'));
                }
            } else if ($mouvement === 'sortie') {
                $qb->andWhere('a.mouvement = false');
                if ($request->query->get('client')) {
                    $qb->andWhere('a.client = :client')->setParameter('client', $request->query->get('client'));
                }
            }
        }
        if ($request->query->get('article')) {
            $qb->andWhere('a.article = :article')->setParameter('article', $request->query->get('article'));
        }
        $dateFormat = $this->get('app.format_date');
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($startDateCreation) {
                $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
            }
        }
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($endDateCreation) {
                $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
            }
        }
        return $qb;
    }

    /**
     * Lists all services entities.
     *
     * @Route("/", name="services_index", methods={"GET"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);

        if (!$request->get('sort') or $request->get('sort') !== 'a.createdAt') {
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        $articles = $em->getRepository('App\\Entity\\Article')->findAllServicesAndProduitsNonStockables();
        $clients = $em->getRepository('App\\Entity\\Client')->findAll();
        $fournisseurs = $em->getRepository('App\\Entity\\Fournisseur')->findAll();
        return $this->render('services/index.html.twig', array(
                    'pagination' => $pagination,
                    'articles' => $articles,
                    'clients' => $clients,
                    'fournisseurs' => $fournisseurs
        ));
    }
    
    /**
     * Lists all stocks entities.
     *
     * @Route("/export/xls", name="service_export_xls", methods={"GET"})
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'services' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des mouvements des services ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Désignation')
                ->setCellValue('B4', 'Mouvement')
                ->setCellValue('C4', 'Type document')
                ->setCellValue('D4', 'Tier')
                ->setCellValue('E4', 'Code tier')
                ->setCellValue('F4', 'Raison social tier')
                ->setCellValue('G4', 'Code Article')
                ->setCellValue('H4', 'Désignation Article')
                ->setCellValue('I4', 'Quantité')
                ->setCellValue('J4', 'Total TTC')
                ->setCellValue('K4', 'Date création')
                ->setCellValue('L4', 'Note');
        $i = 5;
        foreach ($entities as $entity) {
            $codeTier='';$rsTier='';
            if($entity->getFournisseur()){
                $codeTier=$entity->getFournisseur()->getCode();
                $rsTier=$entity->getFournisseur()->getCode();
            }elseif($entity->getClient()){
                $codeTier=$entity->getClient()->getCode();
                $rsTier=$entity->getClient()->getCode();
            }
            $sheet->setCellValue('A' . $i, $entity->getDesignation())
                    ->setCellValue('B' . $i, $entity->getMouvement()?'Entré':'Sortie')
                    ->setCellValue('C' . $i, $entity->getTypeDoc())
                    ->setCellValue('D' . $i, $entity->getFournisseur()?'Fournisseur':'Client')
                    ->setCellValue('E' . $i, $codeTier)
                    ->setCellValue('F' . $i, $rsTier)
                    ->setCellValue('G' . $i, $entity->getArticle()->getCode())
                    ->setCellValue('H' . $i, $entity->getArticle()->getDesignation())
                    ->setCellValue('I' . $i, $entity->getQte())
                    ->setCellValue('J' . $i, $entity->getTtc())
                    ->setCellValue('K' . $i, $entity->getDateCreation())
                    ->setCellValue('L' . $i, $entity->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Mouvements de stock');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);
        
        //custom config
        $this->get('app.excel_custom_config')->setTitre($phpExcelObject);
        $this->get('app.excel_custom_config')->setHeader($phpExcelObject);
        $this->get('app.excel_custom_config')->setBody($phpExcelObject);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename
        );
        $response->headers->set('Content-Type', 'text/xls; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

}
