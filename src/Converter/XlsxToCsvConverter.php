<?php

namespace App\Converter;

use PhpOffice\PhpSpreadsheet\IOFactory;


class XlsxToCsvConverter
{
    public function convert(string $Filename): string
    {
        // Load the XLSX file
        $spreadsheet = IOFactory::load($Filename);
        // Save it as a CSV file
        $writer = IOFactory::createWriter($spreadsheet, 'Csv');
        // The new filename will be the same as the old, except with a different extension
        $newFilename = pathinfo($Filename, PATHINFO_FILENAME) .'.csv';
        $writer->save($newFilename);

        return $newFilename;
    }
}
