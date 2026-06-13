<?php

namespace App\Controller;

use App\Entity\Article;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Article controller.
 *
 * @Route("article")
 */
class ArticleController extends BaseController {

    public function getTitreByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('code')) {
            $chaine .= 'Code contient ' . $request->query->get('code');
            $i++;
        }
        if ($request->query->get('designation')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Désignation contient : ' . $request->query->get('designation');
        }
        if ($request->query->get('stockable')) {
            $i > 0 ? $chaine .= ',' : $i++;
            if ($request->query->get('stockable')==='2') {
                $chaine .= 'Stockable';
            } elseif($request->query->get('stockable')==='1') {
                $chaine .= 'Non Stockable';
            }
        }
        if ($request->query->get('categorie')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $categorie = $em->getRepository('App\\Entity\\Categorie')->findOneById($request->query->get('categorie'));
            $chaine .= 'Catégorie : ' . $categorie;
        }
        if ($request->query->get('famille')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $famille = $em->getRepository('App\\Entity\\Famille')->findOneById($request->query->get('famille'));
            $chaine .= 'Famille : ' . $famille;
        }
        if ($request->query->get('sousfamille')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $sousfamille = $em->getRepository('App\\Entity\\Sousfamille')->findOneById($request->query->get('sousfamille'));
            $chaine .= 'Sous famille : ' . $sousfamille;
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getQbByParametres($em, $request) {
        $qb = $em->getRepository('App\\Entity\\Article')->createQueryBuilder('a');
        $qb->leftJoin('a.categorie', 'cat');
        //filter
        if ($request->query->get('code')) {
            $qb->where('a.code like :code')->setParameter('code', '%' . $request->query->get('code') . '%');
        }
        if ($request->query->get('designation')) {
            $qb->andWhere('a.designation like :designation')->setParameter('designation', '%' . $request->query->get('designation') . '%');
        }

        if ($request->query->get('stockable')) {
            $qb->andWhere('a.stockable = :stockable')->setParameter('stockable', $request->query->get('stockable') - 1);
        }

        if ($request->query->get('sousfamille')) {
            $qb->andWhere('a.sousfamille = :sousfamille')->setParameter('sousfamille', $request->query->get('sousfamille'));
        } else {
            if ($request->query->get('famille')) {
                $qb->andWhere('a.famille = :famille')->setParameter('famille', $request->query->get('famille'));
            } else {
                if ($request->query->get('categorie')) {
                    $qb->andWhere('a.categorie = :categorie')->setParameter('categorie', $request->query->get('categorie'));
                }
            }
        }
        if ($request->query->get('alertStock') and $request->query->get('ruptureStock')) {
            
        } else {
            if ($request->query->get('alertStock')) {
                $qb->andWhere('a.stockable=true');
                $qb->andWhere('a.qteEnStock >0');
                $qb->andWhere('a.seuilAlert>a.qteEnStock');
            }
            if ($request->query->get('ruptureStock')) {
                $qb->andWhere('a.stockable=true');
                $qb->andWhere('a.qteEnStock is not NULL');
                $qb->andWhere('a.qteEnStock<=0');
            }
        }
        return $qb;
    }

    /**
     * Lists all article entities.
     *
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        if (!$request->get('sort')) {
            if ($request->get('customSort')) {
                
            }
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        $categories = $em->getRepository('App\\Entity\\Categorie')->findAll();
        $familles = $em->getRepository('App\\Entity\\Famille')->findAll();
        $sousfamilles = $em->getRepository('App\\Entity\\Sousfamille')->findAll();

        return $this->render('article/index.html.twig', array(
                    'paginator' => $paginator,
                    'pagination' => $pagination,
                    'categories' => $categories,
                    'familles' => $familles,
                    'sousfamilles' => $sousfamilles
        ));
    }

    /**
     * Lists all article entities.
     *
     * @Route("/export/xls", name="article_export_xls", methods={"GET"})
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'articles' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des articles ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Code')
                ->setCellValue('B4', 'Designation')
                ->setCellValue('C4', 'Unité')
                ->setCellValue('D4', 'Catégorie')
                ->setCellValue('E4', 'Famille')
                ->setCellValue('F4', 'Sous famille')
                ->setCellValue('G4', 'Prix achat')
                ->setCellValue('H4', 'Marge')
                ->setCellValue('I4', 'Prix vente ht')
                ->setCellValue('J4', 'Tva')
                ->setCellValue('K4', 'Prix vente ttc')
                ->setCellValue('L4', 'Stockable')
                ->setCellValue('M4', 'Service')
                ->setCellValue('N4', 'Code fournisseur')
                ->setCellValue('O4', 'Rs fournisseur')
                ->setCellValue('P4', 'Qte en depart')
                ->setCellValue('Q4', 'Qte en stock')
                ->setCellValue('R4', 'Seuil alert')
                ->setCellValue('S4', 'Date ajout')
                ->setCellValue('T4', 'Note');
        $i = 5;
        foreach ($entities as $article) {
            $sheet->setCellValue('A' . $i, $article->getCode())
                    ->setCellValue('B' . $i, $article->getDesignation())
                    ->setCellValue('C' . $i, $article->getUnite())
                    ->setCellValue('D' . $i, $article->getCategorie())
                    ->setCellValue('E' . $i, $article->getFamille())
                    ->setCellValue('F' . $i, $article->getSousFamille())
                    ->setCellValue('G' . $i, $article->getPrixAchat())
                    ->setCellValue('H' . $i, $article->getMarge())
                    ->setCellValue('I' . $i, $article->getPrixVenteHt())
                    ->setCellValue('J' . $i, $article->getTva())
                    ->setCellValue('K' . $i, $article->getPrixVenteTtc())
                    ->setCellValue('L' . $i, $article->getStockable() ? 'OUI' : 'NON')
                    ->setCellValue('M' . $i, $article->getService() ? 'OUI' : 'NON')
                    ->setCellValue('N' . $i, $article->getFournisseur() ? $article->getFournisseur()->getCode() : '')
                    ->setCellValue('O' . $i, $article->getFournisseur() ? $article->getFournisseur()->getRs() : '')
                    ->setCellValue('P' . $i, $article->getQteEnDepart())
                    ->setCellValue('Q' . $i, $article->getQteEnStock())
                    ->setCellValue('R' . $i, $article->getSeuilAlert())
                    ->setCellValue('S' . $i, $article->getDateAjout())
                    ->setCellValue('T' . $i, $article->getNote());
            $i++;
        }

        $phpExcelObject->getActiveSheet()->setTitle('Articles');
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

    /**
     * Creates a new article entity.
     *
     * @Route("/new", name="article_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $article = new Article();
        $form = $this->createForm('App\Form\ArticleType', $article);
        $form->add('stockable', CheckboxType::class, array(
            'label' => 'Stockable ?',
            'required' => false
        ));
        $form->add('service', CheckboxType::class, array(
            'label' => 'Service ?',
            'required' => false
        ));
        $form->add('fournisseur', EntityType::class, ['attr' => ['class' => 'selectpicker', 'data-live-search' => true, 'title' => 'Chercher et sélectionner'], 'class' => 'App\\Entity\\Fournisseur']);
        $form->add('qteEnDepart');
        $form->add('qteEnStock', TextType::class, ['attr' => ['readonly' => true]]);
        $form->add('seuilAlert');
        $form->add('dateAjout', DateType::class, array(
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'dd/MM/yyyy',
            'required' => false,
            'attr' => array('class' => 'datepicker'),
            'data' => new \DateTime()
        ));



        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($article->getSousfamille()) {
                $article->setFamille($article->getSousfamille()->getFamille());
            }
            $em->persist($article);
            $em->flush($article);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('article_new');
            }
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', array(
                    'article' => $article,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to show an existing article entity.
     *
     * @Route("/{id}/show", name="article_show", methods={"GET"})
     */
    public function showAction(Article $article) {
        return $this->render('article/show.html.twig', array(
                    'article' => $article,
        ));
    }

    /**
     * Displays a form to edit an existing article entity.
     *
     * @Route("/{id}/edit", name="article_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Article $article) {
        $editForm = $this->createForm('App\Form\ArticleType', $article);
        if ($article->getStockable()) {
            $editForm->add('seuilAlert', TextType::class, ['required' => false]);
        }

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($article->getMedia()) {
                $article->getMedia()->setName(sha1(uniqid(mt_rand(), true)));
            }
            if ($article->getSousfamille()) {
                $article->setFamille($article->getSousfamille()->getFamille());
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', array(
                    'article' => $article,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a article entity.
     *
     * @Route("/{id}/delete", name="article_delete", methods={"GET"})
     */
    public function deleteAction(Article $article) {
        if ($article) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($article);
                $em->flush($article);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('article_index');
    }

    //api

    /**
     * Get article
     *
     * @Route("/api", name="article", methods={"GET"})
     */
    public function getArticleAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $article = $em->getRepository('App\\Entity\\Article')->find($id);

        $serializer = $this->container->get('serializer');

        $response = array(
            'success' => true,
            'article' => $serializer->serialize($article, 'json', array('groups' => array('article')))
        );
        return new JsonResponse($response);
    }

    /**
     * Creates a new custom article entity.
     *
     * @Route("/custom/new", name="article_custom_new", methods={"GET", "POST"})
     */
    public function customNewAction(Request $request) {
        $article = new Article();
        $form = $this->createForm('App\Form\Custom\ArticleType', $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush($article);
            //a redefinir
            return new JsonResponse(array(
                'success' => true,
                'articleId' => $article->getId(),
                'code' => $article->getCode(),
                'designation' => $article->getDesignation()
            ));
        }
        return $this->render('article/custom/new.html.twig', array(
                    'article' => $article,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new custom produit entity.
     *
     * @Route("/produit/custom/new", name="produit_custom_new", methods={"GET", "POST"})
     */
    public function customProduitNewAction(Request $request) {
        $article = new Article();
        $article->setService(false);
        $article->setStockable(true);
        $form = $this->createForm('App\Form\Custom\ArticleType', $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush($article);
            //a redefinir
            return new JsonResponse(array(
                'success' => true,
                'articleId' => $article->getId(),
                'code' => $article->getCode(),
                'designation' => $article->getDesignation()
            ));
        }
        return $this->render('article/custom/produitNew.html.twig', array(
                    'article' => $article,
                    'form' => $form->createView(),
        ));
    }

}
