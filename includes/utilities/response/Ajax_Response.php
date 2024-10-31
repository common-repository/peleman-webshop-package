<?php

declare(strict_types=1);

namespace PWP\includes\utilities\response;

abstract class Ajax_Response implements I_Response
{
    protected string $message;
    protected int $code;
    /**
     * TODO: build class into proper AJAX response class
     * 
     */
    public function to_array(): array
    {
        return [];
    }

    public function get_code(): int
    {
        return $this->code;
    }

    public function get_message(): string
    {
        return $this->message;
    }
}
