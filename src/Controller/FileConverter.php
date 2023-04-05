<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Converter\CsvToXlsxConverter;
use App\Converter\XlsxToCsvConverter;



class FileConverter extends AbstractController
{

    #[Route('/')]

    function index(Request $request): Response
    {
        // Check if the user has clicked one of the buttons
        $csvToXlsxButtonClicked = $request->request->has('csv_to_xlsx');
        $xlsxToCsvButtonClicked = $request->request->has('xlsx_to_csv');
        // If neither button has been clicked, render the initial form
        if (!$csvToXlsxButtonClicked && !$xlsxToCsvButtonClicked) {
            return $this->render('conversion/index.html.twig');
        }
        // Get the uploaded file
        $uploadedFile = $request->files->get('file');
        // If no file has been uploaded, render the form with an error message
        if (!$uploadedFile) {
            return $this->render('conversion/index.html.twig', [
                'error' => 'Please choose a file to upload.'
            ]);
        }
        // Generate a new, unique filename for the uploaded file
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $Filename = $originalFilename.'.'.uniqid().$uploadedFile->guessExtension();

        try {
            // Move the uploaded file to the uploads directory
            $uploadedFile->move(
                $this->getParameter('uploads_directory'),
                $Filename
            );
        } catch (FileException $e) {
            return $this->render('conversion/index.html.twig', [
                'error' => 'An error occurred while uploading your file: ' . $e->getMessage()
            ]);
        }
        //dd($uploadedFile);
        // If the user clicked the "Convert CSV to XLSX" button, convert the file
        $converter = $csvToXlsxButtonClicked ? new CsvToXlsxConverter() : new XlsxToCsvConverter();
        $convertedFilename = $converter->convert($this->getParameter('uploads_directory').'/'. $Filename);
        // Send the converted file to the user
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $convertedFilename);
        $response->setContent(file_get_contents($convertedFilename));
        $response->send();

        return $response;
    }
}