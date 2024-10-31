<?php

declare(strict_types=1);

namespace PWP\includes\utilities\notification;

use PWP\includes\utilities\response\I_Response;
use WP_REST_Response;

class Error_Notice implements I_Notice
{
    private string $message;
    private string $description;
    private array $data;
    private ?\Exception $cause;

    public function __construct(string $message, string $description, array $data = [], ?\Exception $cause = null)
    {
        $this->message = $message;
        $this->description = $description;
        $this->data = $data;
        $this->cause = $cause;
    }

    public function get_message(): string
    {
        return $this->message;
    }

    public function get_description(): string
    {
        return $this->description;
    }

    public function get_cause(): \Exception
    {
        return $this->cause;
    }

    public function get_data(): array
    {
        return $this->data;
    }

    public function to_array(): array
    {
        $response = array(
            "error" => $this->message,
            "description" => $this->description,
        );

        if (!empty($this->data)) {
            $response += $this->data;
        }

        if (!is_null($this->cause)) {
            $response['cause'] = array(
                'message' => $this->cause->getMessage(),
                'code' => $this->cause->getCode(),
            );
        }
        return $response;
    }

    public function to_rest_response(): WP_REST_Response
    {
        return new WP_REST_Response($this->to_array(), 200);
    }

    public function add_response_component(I_Notice $response): void
    {
    }

    public function is_success(): bool
    {
        return false;
    }

    public function __tostring(): string
    {
        return "{$this->message}: {$this->description}";
    }
}
