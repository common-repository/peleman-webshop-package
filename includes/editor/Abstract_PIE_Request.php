<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use PWP\includes\Abstract_Request;
use PWP\includes\exceptions\Invalid_Response_Exception;

/**
 * Abstract base class for any request to the PIE editor
 */
abstract class Abstract_PIE_Request extends Abstract_Request
{
    private Editor_Auth_Provider $auth;
    private string $endpoint;
    private string $method;
    private int $timeouts;

    public function __construct(Editor_Auth_Provider $auth, string $endpoint)
    {
        $this->auth         = $auth;
        $this->endpoint     = $endpoint;
        $this->timeouts     = 5;
        $this->redirects    = 5;
        $this->set_GET();
    }

    /**
     * Get full URL of the API endpoint this class calls
     *
     * @return string
     */
    final protected function get_endpoint_url(): string
    {
        return $this->auth->get_domain() . $this->endpoint;
    }
	
public function set_endpoint_url($newEndpoint):void
    {
          $this->endpoint .= '?organisationid=' . $newEndpoint;
    }

    final protected function get_api_key(): string
    {
        return $this->auth->get_api_key();
    }

    final protected function get_customer_id(): string
    {
        return $this->auth->get_customer_id();
    }

    final protected function set_GET(): void
    {
        $this->method = 'GET';
    }

    final protected function set_POST(): void
    {
        $this->method = 'POST';
    }

    final public function get_method(): string
    {
        return $this->method;
    }

    /**
     * Make request to endpoint
     *
     * @return object wp_remote_request response array. See documentation for details.
     * @throws Invalid_Response_Exception on a `wp_error`/`null`/`false` response
     */
    public function make_request(): object
    {
        $url = $this->auth->get_domain() . $this->endpoint;
        $response = wp_remote_request($url, array(
            'method' => $this->method,
            'timeout' => $this->timeout,
            'redirection' => $this->redirects,
            'headers' => $this->generate_request_header(),
            'body' => $this->generate_request_body(),
        ));

        if (!$response) {
        }
        if (is_wp_error($response)) {
            error_log($response->get_error_code() . ": " . $response->get_error_message());
            throw new Invalid_Response_Exception(__("Could not connect to API.", 'Peleman-Webshop-Package'));
        }
        //TODO: improve response handling
        return json_decode(json_encode($response));
    }

    protected abstract function generate_request_body(): array;

    /**
     * Generates basic request header with authentication
     *
     * @return array
     */
    protected function generate_request_header(): array
    {
        return $this->auth->get_auth_header();
    }
}
