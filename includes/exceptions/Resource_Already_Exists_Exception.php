<?php

declare(strict_types=1);

namespace PWP\includes\exceptions;

class Resource_Already_Exists_Exception extends API_Exception
{
    public function __construct(string $message = '', \Throwable $previous = null)
    {
        parent::__construct(!empty($message) ? $message : 'resource already exists', 400, $previous);
    }
}