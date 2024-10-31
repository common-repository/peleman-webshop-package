<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\wrappers\PDF_Upload;
use PWP\includes\utilities\notification\I_Notification;

class Validate_File_PageCount extends File_Validator
{
    private int $minPages;
    private int $maxPages;
    
    public function __construct(int $minPages, int $maxPages)
    {
        parent::__construct();
        $this->minPages = $minPages;
        $this->maxPages = $maxPages;
    }

    public function handle(PDF_Upload $data, ?I_Notification $notification = null): bool
    {
        $pages = $data->get_page_count();

        if ($pages > $this->maxPages) {
            $notification->add_error('too many pages', 
            __('File has more than the maximum allowed page count.', 'Peleman-Webshop-Package'));
            return false;
        }
        if ($pages < $this->minPages) {
            $notification->add_error('too few pages', __('File is below the minimum allowed page count.',  'Peleman-Webshop-Package'));
            return false;
        }

        return $this->handle_next($data, $notification);
    }
}
