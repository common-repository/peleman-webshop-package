<?php

declare(strict_types=1);

namespace PWP\includes\exceptions;

class WP_Error_Exception extends \Exception
{
    public function __construct(\WP_Error $wpError, \Throwable $previous = null)
    {
        parent::__construct($wpError->get_error_message(), $wpError->get_error_code(), $previous);
    }
}
