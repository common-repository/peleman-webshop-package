<?php

declare(strict_types=1);

namespace PWP\includes\API\endpoints;

/**
 * abstract endpoint class for POST requests
 */
abstract class Abstract_CREATE_Endpoint extends Endpoint_Controller
{
    final public function get_methods(): string
    {
        return \WP_REST_Server::CREATABLE;
    }
}
