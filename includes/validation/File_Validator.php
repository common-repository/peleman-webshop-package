<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\utilities\notification\I_Notification;
use PWP\includes\wrappers\PDF_Upload;

abstract class File_Validator
{
    private ?self $next = null;

    public function __construct()
    {
        $this->next = null;
    }

    final public function set_next(File_Validator $next): File_Validator
    {
        $this->next = $next;
        return $this->next;
    }

    abstract public function handle(PDF_Upload $data, ?I_Notification $notification = null): bool;

    final protected function handle_next(PDF_Upload $file, ?I_Notification $notification = null): bool
    {
        return is_null($this->next)
            ? ($notification->is_success() ?? true)
            : $this->next->handle($file, $notification);
    }
}
