<?php

declare(strict_types=1);

namespace PWP\includes\utilities\notification;

use PWP\includes\utilities\response\I_Response;

class Notification implements I_Notification
{
    private array $errors;
    private bool $isSuccess;
    private array $warnings;

    public function __construct()
    {
        $this->isSuccess = true;
        $this->errors = array();
        $this->warnings = array();
        $this->isSuccess = true;
    }

    final public function add_error(string $error, string $description, array $data = [], \Exception $cause = null): self
    {
        $this->errors[] = new Error_Notice($error, $description, $data, $cause);
        $this->set_failed();
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Error_Notice[]
     */
    final public function get_errors(): array
    {
        return $this->errors;
    }

    final protected function set_failed(): void
    {
        $this->isSuccess = false;
    }

    final public function is_success(): bool
    {
        return $this->isSuccess;
    }

    public function to_array(): array
    {
        $response = array();

        foreach ($this->errors as $key => $error) {
            $response['errors'][$key] = $error->to_array();
        }

        return $response;
    }

    public function add_response_component(I_Response $response): void
    {
        //FIXME: add functionality
    }
}
