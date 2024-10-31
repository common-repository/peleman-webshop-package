<?php

declare(strict_types=1);

namespace PWP\includes\utilities\pdfHandling;

use PWP\includes\wrappers\PDF_Upload;

interface I_PDF_Data
{
    public static function generate_from_upload(array $upload): PDF_Upload;
}
