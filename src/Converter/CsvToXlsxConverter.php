<?php

namespace App\Converter;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CsvToXlsxConverter
{
    public function convert(string $Filename): string
    {
        // Load the CSV file
        $spreadsheet = IOFactory::load($Filename);
        // Save it as an XLSX file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        // The new filename will be the same as the old, except with a different extension
        $newFilename = pathinfo($Filename, PATHINFO_FILENAME).'.xlsx';
        //save it to the uploads directory
        $writer->save($newFilename);

        return $newFilename;
    }
}
