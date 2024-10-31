<?php

declare(strict_types=1);

namespace PWP\includes\utilities\response;

use PWP\includes\utilities\notification\I_Notice;

class Error_Response implements I_Response
{
    private int $code;
    private string $message;
    private array $data;

    public function __construct(string $message, int $code = 400, array $data = [])
    {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }

    public function set_data(array $data): void
    {
        $this->data = $data;
    }

    public function get_data(): array
    {
        return $this->data;
    }

    public function add_data(string $key, $data): void
    {
        $this->data[$key] = $data;
    }

    public function to_array(): array
    {
        $array = array(
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
        );


        return $array;
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
