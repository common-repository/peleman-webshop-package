<?php

declare(strict_types=1);

namespace PWP\includes\API\endpoints;

use PWP\includes\authentication\I_Api_Authenticator;
use PWP\includes\exceptions\Invalid_Input_Exception;
use PWP\includes\hookables\abstracts\I_Hookable_Component;
use WP_REST_Server;

abstract class Endpoint_Controller implements I_Endpoint, I_Hookable_Component
{
    private string $namespace;
    protected string $path;
    protected string $title;
    private int $priority;

    protected string $object;

    public function __construct(string $namespace, string $path, string $title, int $priority = 10)
    {
        $this->namespace = $namespace;
        $this->path = $path;
        $this->title = $title;
        $this->priority = $priority;
    }

    public function get_permission_callback(): callable
    {
        return array($this, 'authenticate');
    }

    final public function get_path(): string
    {
        return $this->path;
    }

    final public function register(): void
    {
        add_action('rest_api_init', array($this, 'register_endpoint'), $this->priority, 1);
    }

    final public function register_endpoint(WP_REST_Server $restServer): void
    {
        register_rest_route(
            $this->namespace,
            $this->path,
            array(
                array(
                    'methods' => $this->get_methods(),
                    'callback' => array($this, 'do_action'),
                    'permission_callback' => $this->get_permission_callback(),
                    'args' => $this->get_arguments(),
                ),
                'schema' => array($this, 'get_schema'),
            ),
        );
    }

    /**
     * helper function for validing REST api requests with a json schema,
     * combining the functionality of both the 
     * `rest_validate_value_from_schema` and `rest_sanitize_value_from_schema` methods
     *
     * @param array $request request array body
     * @param string $name name of the object to be used in error/validation messages
     * @return array returns validated & sanitized request array
     * @throws Invalid_Input_Exception thrown if validation fails due to missing or incorrect parameter(s)
     */
    final protected function validate_request_with_schema(array $request, string $name = ''): array
    {
        $schema = $this->get_schema();
        $result = rest_validate_value_from_schema($request, $schema, $name);
        if (is_wp_error($result)) {
            throw new Invalid_Input_Exception($result->get_error_message());
        }

        $request = rest_sanitize_value_from_schema($request, $schema, $name);
        if (is_wp_error($request)) {
            throw new Invalid_Input_Exception($request->get_error_message());
        }

        return $request;
    }

    public function get_arguments(): array
    {
        return [];
    }

    public function get_schema(): array
    {
        return [];
    }

    public abstract function do_action(\WP_REST_Request $request): \WP_REST_Response;
    public abstract function authenticate(\WP_REST_Request $request): bool;
}
