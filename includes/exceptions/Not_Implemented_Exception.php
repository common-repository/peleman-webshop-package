<?php

declare(strict_types=1);

namespace PWP\includes\exceptions;

class Not_Implemented_Exception extends API_Exception
{
    public function __construct(string $methodName, \Throwable $previous = null)
    {
        parent::__construct("method {$methodName} not implemented in file {$this->getFile()} > {$this->getLine()}", 501, $previous);
    }
}
