<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\utilities\notification\I_Notification;
use PWP\includes\wrappers\PDF_Upload;

class Validate_File_Errors extends File_Validator
{
    final public function handle(PDF_Upload $file, ?I_Notification $notification = null): bool
    {
        if (!empty($file->get_error())) {
            $notification->add_error(
                __("file upload error", 'Peleman-Webshop-Package'),
                $this->code_to_message($file->get_error())
            );
        }
        return $this->handle_next($file, $notification);
    }

    private function code_to_message(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return  __("The uploaded file exceeds the upload maximum file size directive.", 'Peleman-Webshop-Package');
            case UPLOAD_ERR_FORM_SIZE:
                return __("The uploaded file exceeds the maximum file size specified in the HTML form.", 'Peleman-Webshop-Package');
            case UPLOAD_ERR_PARTIAL:
                return __("The file was only partially uploaded.", 'Peleman-Webshop-Package');
            case UPLOAD_ERR_NO_FILE:
                return __("No file was uploaded.", 'Peleman-Webshop-Package');
            case UPLOAD_ERR_NO_TMP_DIR:
                return __("Temporary folder missing.", 'Peleman-Webshop-Package');
            case UPLOAD_ERR_CANT_WRITE:
                return __("Failed to write file to disk.", 'Peleman-Webshop-Package');
            case UPLOAD_ERR_EXTENSION:
                return __("File upload stopped by extension.", 'Peleman-Webshop-Package');
            default:
                return __("Unknown upload error.", 'Peleman-Webshop-Package');
        }
    }
}
