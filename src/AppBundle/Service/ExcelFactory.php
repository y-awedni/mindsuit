<?php

namespace AppBundle\Service;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Drop-in replacement for the (abandoned) liuggio/excelbundle "phpexcel"
 * service, backed by phpoffice/phpspreadsheet. Keeps the same method
 * signatures so existing export controllers work unchanged.
 */
class ExcelFactory
{
    public function createPHPExcelObject()
    {
        return new Spreadsheet();
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param string      $type Legacy PHPExcel type name (e.g. "Excel5", "Excel2007")
     */
    public function createWriter(Spreadsheet $spreadsheet, $type = 'Xlsx')
    {
        return IOFactory::createWriter($spreadsheet, $this->normalizeType($type));
    }

    /**
     * @param IWriter $writer
     */
    public function createStreamedResponse(IWriter $writer, $status = 200, array $headers = [])
    {
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $status,
            $headers
        );

        return $response;
    }

    private function normalizeType($type)
    {
        $map = [
            'Excel5' => 'Xls',
            'Excel2007' => 'Xlsx',
            'OOCalc' => 'Ods',
            'CSV' => 'Csv',
            'HTML' => 'Html',
            'PDF' => 'Mpdf',
        ];

        return isset($map[$type]) ? $map[$type] : $type;
    }
}
