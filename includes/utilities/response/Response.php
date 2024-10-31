<?php

declare(strict_types=1);

namespace PWP\includes\utilities\response;

use PWP\includes\utilities\notification\I_Notice;

class Response implements I_Response, I_Notice
{
    private bool $successful;

    private int $httpCode;
    private array $data;
    protected string $message;
    protected string $description;
    /**
     * @var I_Notice[]
     */
    private array $components;


    public function __construct(
        string $message,
        string $description,
        bool $success = true,
        int $httpCode = 200,
        array $additionalData = []
    ) {
        $this->message = $message;
        $this->description = $description;
        $this->successful = $success;
        $this->httpCode = $httpCode;
        $this->data = $additionalData;
        $this->components = array();
    }

    final public function add_response(I_Notice $response): void
    {
        $this->components[] = $response;
    }

    final public function get_code(): int
    {
        return $this->httpCode;
    }

    final public function get_data(): array
    {
        return $this->data;
    }

    final public function add_response_component(I_Notice $response): void
    {
        $this->components[] = $response;
    }

    final public function get_components(): array
    {
        return $this->components;
    }

    final public function get_message(): string
    {
        return $this->message;
    }

    final public function get_description(): string
    {
        return $this->description;
    }
    public function to_array(): array
    {
        $response = array(
            'status'        => $this->successful ? 'success' : 'failure',
            'message'       => $this->message,
            'description'   => $this->description,
        );

        if (!empty($this->data)) {
            $responseData = array();
            foreach ($this->data as $key => $data) {
                $responseData[$key] = $data;
            }
            $response['data'] = $responseData;
        }

        foreach ($this->components as $key => $component) {
            $response[] = $component->to_array();
        }

        return $response;
    }

    public static function success(string $message, string $description, int $httpCode = 200, array $additionalData = []): self
    {
        return new self($message, $description, true, $httpCode, $additionalData);
    }

    public static function failure(string $message,  string $description, int $httpCode = 400, array $additionalData = []): self
    {
        return new self($message, $description, false, $httpCode, $additionalData);
    }

    public function is_success(): bool
    {
        return $this->successful;
    }
}
