<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Comptabilite controller.
 *
 * @Route("comptabilite")
 */
class ComptabiliteController extends Controller {

    /**
     * Lists all article entities.
     *
     * @Route("/", name="comptabilite_index", methods={"GET","POST"})
     */
    public function indexAction(Request $request) {
        $date = date("d-m-Y");
        $sys_date = new \DateTime($date);
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
                ->add('DateDebut', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'attr' => array('class' => 'datepicker'),
                    'data' => $sys_date->modify("-30 days")
                ))
                ->add('DateFin', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'attr' => array('class' => 'datepicker'),
                    'data' => new \DateTime()
                ))
                ->add('saveDepences', SubmitType::class, array('label' => 'Exporter les dépenses en csv', 'attr' => ['class' => 'btn-danger']))
                ->add('saveRevenus', SubmitType::class, array('label' => 'Exporter les revenus en csv', 'attr' => ['class' => 'btn-success']))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $dateDebut = $data['DateDebut'];
            $dateFin = $data['DateFin'];
            $mouvements = null;
            $autresMvts = null;
            $typeMvt = null;
            $em = $this->getDoctrine()->getManager();
            if ($form->get('saveDepences')->isClicked()) {
                $typeMvt = 'depenses';
                $mouvements = $em->getRepository('App\\Entity\\Mouvement')->findAllDepencesByInterval($dateDebut, $dateFin);
                $autresMvts = $em->getRepository('App\\Entity\\BonReception')->findAllDepencesByInterval($dateDebut, $dateFin);
            } else {
                $typeMvt = 'revenus';
                $mouvements = $em->getRepository('App\\Entity\\Mouvement')->findAllRevenusByInterval($dateDebut, $dateFin);
                $autresMvts = $em->getRepository('App\\Entity\\Facture')->findAllRevenusByInterval($dateDebut, $dateFin);
            }
            if (count($mouvements) + count($autresMvts) > 0) {
                return $this->export($mouvements, $autresMvts, $typeMvt, date_format($dateDebut, 'd-m-Y'), date_format($dateFin, 'd-m-Y'));
            }
        }

        return $this->render('comptabilite/index.html.twig', array(
                    'form' => $form->createView()
        ));
    }
    
    public function exportEnCsv() {
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A1', 'title1')
                ->setCellValue('A2', 'title2')
                ->setCellValue('B1', 'text1')
                ->setCellValue('B2', 'text2');
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'stream-file.xls'
        );
        $response->headers->set('Content-Type', 'text/xls; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    public function export($mouvements, $autresMvts, $typeMvt, $dateDebut, $dateFin) {
        $totalTTC = 0;
        $totalTVA = 0;
        $filename = $typeMvt . '__' . $dateDebut . '__' . $dateFin . '.xls';
        $rows = array();
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Désignation')
                ->setCellValue('B1', 'Type document')
                ->setCellValue('C1', 'Tier')
                ->setCellValue('D1', 'Total TTC')
                ->setCellValue('E1', 'Total TVA')
                ->setCellValue('F1', 'Date de création');
        $i = 2;
        foreach ($mouvements as $mouvement) {
            if (!$mouvement->getClient() && !$mouvement->getFournisseur()) {
                $date = '';
                if ($mouvement->getDateCreation()) {
                    $date = date_format($mouvement->getDateCreation(), "d-m-Y");
                }
                $totalTTC += $mouvement->getTtc();
                $sheet->setCellValue('A' . $i, $mouvement->getDesignation())
                        ->setCellValue('B' . $i, $mouvement->getTypeDoc())
                        ->setCellValue('C' . $i, $mouvement->getTier())
                        ->setCellValue('D' . $i, number_format($mouvement->getTtc(), '3', ',', ''))
                        ->setCellValue('E' . $i, '')
                        ->setCellValue('F' . $i, $date);
                $i++;
            }
        }
        
        foreach ($autresMvts as $autresMvt) {
            if ($typeMvt === 'depenses') {
                $date = '';
                if ($autresMvt->getDateReception()) {
                    $date = date_format($autresMvt->getDateReception(), "d-m-Y");
                }
                $sheet->setCellValue('A' . $i, $autresMvt->getCode())
                        ->setCellValue('B' . $i, 'br')
                        ->setCellValue('C' . $i, $autresMvt->getFournisseur()->getMf())
                        ->setCellValue('D' . $i, number_format($autresMvt->getTotal(),'3',',',''))
                        ->setCellValue('E' . $i, number_format($autresMvt->getTva(),'3',',',''))
                        ->setCellValue('F' . $i, $date);
                $i++;
            } else {
                $date = '';
                if ($autresMvt->getDateCreation()) {
                    $date = date_format($autresMvt->getDateCreation(), "d-m-Y");
                }
                $sheet->setCellValue('A' . $i, $autresMvt->getCode())
                        ->setCellValue('B' . $i, 'facture')
                        ->setCellValue('C' . $i, $autresMvt->getClient()->getMf())
                        ->setCellValue('D' . $i, number_format($autresMvt->getTotal(), '3', ',', ''))
                        ->setCellValue('E' . $i, number_format($autresMvt->getTva(), '3', ',', ''))
                        ->setCellValue('F' . $i, $date);
                $i++;
            }
            $totalTTC += $autresMvt->getTotal();
            $totalTVA += $autresMvt->getTva();
        }
        $data = array('', '', '', number_format($totalTTC, '3', '.', ' '), number_format($totalTVA, '3', '.', ''), '');
        $sheet->setCellValue('A' . $i, '')
                        ->setCellValue('B' . $i, '')
                        ->setCellValue('C' . $i, '')
                        ->setCellValue('D' . $i, number_format($totalTTC, '3', ',', ''))
                        ->setCellValue('E' . $i, number_format($totalTVA, '3', ',', ''))
                        ->setCellValue('F' . $i, '');
        
        $phpExcelObject->getActiveSheet()->setTitle($typeMvt . '_' . $dateDebut . '_' . $dateFin);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

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
