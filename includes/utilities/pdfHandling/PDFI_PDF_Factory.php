<?php

declare(strict_types=1);

namespace PWP\includes\utilities\pdfHandling;

use PWP\includes\wrappers\PDF_Upload;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PageBoundaries;

class PDFI_PDF_Factory implements I_PDF_Data
{
    public static function generate_from_upload(array $upload): PDF_Upload
    {
        $uploadData = new PDF_Upload($upload);
        $pdf = new Fpdi();

        $pages  = $pdf->setSourceFile($uploadData->get_tmp_name());
        $tpl    = $pdf->importPage(1, PageBoundaries::TRIM_BOX);
        $pdf->AddPage();
        $dimensions = $pdf->getTemplateSize($tpl);

        $width      = round($dimensions['width']);
        $height     = round($dimensions['height']);

        // error_log ("pdf page count: {$pages}");
        // error_log  ("height: {$height} mm");
        // error_log  ("width: {$width} mm");

        $uploadData->set_page_count($pages);
        $uploadData->set_dimensions($width, $height);
        return $uploadData;
    }
}
