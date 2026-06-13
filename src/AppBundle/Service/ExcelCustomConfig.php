<?php

namespace AppBundle\Service;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Description of ExcelCustomConfig
 *
 * @author lead_dev
 */
class ExcelCustomConfig {

    public function setTitre($sheet) {
        $sheet->getActiveSheet()->mergeCells('A1:'.$sheet->getActiveSheet()->getHighestColumn().'1');
        $sheet->getActiveSheet()->mergeCells('A2:'.$sheet->getActiveSheet()->getHighestColumn().'2');
        $elementFont = $sheet->getActiveSheet()->getStyle('A1')->getFont();
        $elementFont->getColor()->setARGB(Color::COLOR_DARKGREEN);
        $elementFont->setSize(18);
        $elementFont->setBold(true);
    }
    public function setHeader($sheet) {
        for ($i = 'A'; $i <= $sheet->getActiveSheet()->getHighestColumn(); $i++) {
            $sheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
            $elementFont = $sheet->getActiveSheet()->getStyle($i . '4')->getFont();
            $elementFont->getColor()->setARGB(Color::COLOR_DARKBLUE);
            $elementFont->setSize(14);
            $elementFont->setBold(true);
        }
    }

    public function setBody($sheet) {
        //rows
        for ($j = 4; $j <= $sheet->getActiveSheet()->getHighestRow(); $j++) {
            if ($j % 2) {
                $sheet->getActiveSheet()->getStyle('A' . $j . ':' . $sheet->getActiveSheet()->getHighestColumn() . $j)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F2F2F2');
            } else {
                $sheet->getActiveSheet()->getStyle('A' . $j . ':' . $sheet->getActiveSheet()->getHighestColumn() . $j)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('ffffff');
            }
            //columns
            for ($i = 'A'; $i <= $sheet->getActiveSheet()->getHighestColumn(); $i++) {
                $sheet->getActiveSheet()->getStyle($i . $j)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getActiveSheet()->getStyle($i . $j)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getActiveSheet()->getStyle($i . $j)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getActiveSheet()->getStyle($i . $j)->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
            }
        }
    }

}
