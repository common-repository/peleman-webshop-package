<?php

declare(strict_types=1);

namespace PWP\includes\exceptions;

class Not_Found_Exception extends API_Exception
{
    public function __construct(string $message = '', \Throwable $previous = null)
    {
        parent::__construct(!empty($message) ? $message : 'object not found', 404, $previous);
    }
}