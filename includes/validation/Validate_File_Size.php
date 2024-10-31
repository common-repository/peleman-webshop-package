<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\wrappers\PDF_Upload;
use PWP\includes\validation\File_Validator;
use PWP\includes\utilities\notification\I_Notification;

final class Validate_File_Size extends File_Validator
{

    /** bytes in a kilobyte */
    public const KB = 1024;
    /** bytes in a megabyte */
    public const MB = 1048576;
    /** bytes in a gigabyte */
    public const GB = 1073741824;

    private int $maxFileSize;

    /**
     * validator for maximum file size.
     *
     * @param integer $maxFileSize maximum file size in bytes
     */
    public function __construct(int $maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    public function handle(PDF_Upload $file, ?I_Notification $notification = null): bool
    {
        if ($file->get_size() <= $this->maxFileSize)
            return $this->handle_next($file, $notification);

        $notification->add_error(
            'file too large',
            __('The file is too large. Please upload a file smaller than the maximum upload size.', 'Peleman-Webshop-Package')
        );
        return false;
    }
}
