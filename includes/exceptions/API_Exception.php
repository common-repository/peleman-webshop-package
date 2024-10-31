<?php

declare(strict_types=1);

namespace PWP\includes\exceptions;

use WP_REST_Response;

class API_Exception extends \Exception
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function to_rest_response(): WP_REST_Response
    {
        return new WP_REST_Response(
            array(
                'code' => $this->getCode(),
                'message' => $this->getMessage(),
            ),
            $this->getCode()
        );
    }
}
