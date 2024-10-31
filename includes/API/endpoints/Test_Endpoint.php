<?php

declare(strict_types=1);

namespace PWP\includes\API\endpoints;

use WP_REST_Request;
use WP_REST_Response;

use PWP\includes\authentication\I_Api_Authenticator;

defined('ABSPATH') || die;

class Test_Endpoint extends Endpoint_Controller
{
    public function __construct(string $namespace)
    {
        parent::__construct(
            $namespace,
            '/test',
            'test',
        );
    }

    public function do_action(WP_REST_Request $request): WP_REST_Response
    {
        return new WP_REST_Response('test successful!', 200);
        // return new WP_REST_Response($results, 200);
    }

    public function authenticate(WP_REST_Request $request): bool
    {
        return true;
    }

    public function get_arguments(): array
    {
        return [];
    }

    public function get_methods(): string
    {
        return \WP_REST_Server::ALLMETHODS;
    }

    public function get_schema(): array
    {
        return [];
    }
}
