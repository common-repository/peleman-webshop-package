<?php

declare(strict_types=1);

namespace PWP\includes\API\endpoints;

/**
 * abstract endpoint class for PUT/PATCH requests
 * 
 */
abstract class Abstract_UPDATE_Endpoint extends Endpoint_Controller
{
    final public function get_methods(): string
    {
        return 'PUT, PATCH';
    }

    public function get_schema(): array
    {
        return [];
    }
}
