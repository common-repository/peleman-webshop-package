<?php

declare(strict_types=1);

namespace PWP\includes\exceptions;

class Invalid_Input_Exception extends API_Exception
{
    public function __construct(string $message = '', \Throwable $previous = null)
    {
        parent::__construct(!empty($message) ? $message : 'input invalid', 404, $previous);
    }
}