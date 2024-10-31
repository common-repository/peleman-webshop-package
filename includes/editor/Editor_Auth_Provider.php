<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use Firebase\JWT\JWT;

final class Editor_Auth_Provider
{
    private string $api_key;
    private string $customer_id;
    private string $domain;

    public function __construct()
    {
        $this->api_key      = get_option('pie_api_key', 'https://deveditor.peleman.com');
        $this->customer_id  = get_option('pie_customer_id', '');
        $this->domain       = rtrim(get_option('pie_domain', ''));
    }

    public function get_domain(): string
    {
        return $this->domain;
    }

    public function get_api_key(): string
    {
        return $this->api_key;
    }

    public function get_customer_id(): string
    {
        return $this->customer_id;
    }

    public function get_signed_url_token(): array
    {
        $payload = [
            'iss'       => get_permalink(),
            'aud'       => $this->domain,
            'sub'       => $this->customer_id,
            'iat'       => current_time('timestamp', true),
        ];

        $jwt = JWT::encode($payload, $this->api_key, 'HS256');
        return ['signature' => $jwt];
    }

    public function get_auth_header(): array
    {
        $header = ['PIEAPIKEY' => $this->api_key];

        return $header;
    }

    public function new_credentials(string $domain, string $api_key, string $customer_id): void
    {
        $this->domain = $domain;
        $this->api_key = $api_key;
        $this->customer_id = $customer_id;
    }
}
