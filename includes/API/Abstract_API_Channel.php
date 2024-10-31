<?php

declare(strict_types=1);

namespace PWP\includes\API;

use PWP\includes\loaders\Plugin_Loader;
use PWP\includes\API\endpoints\I_Endpoint;
use PWP\includes\hookables\abstracts\I_Hookable_Component;
use PWP\includes\authentication\I_Api_Authenticator;

/**
 * abstract class for the handling and registering of API Endpoints.
 */
class Abstract_API_Channel implements I_Hookable_Component
{
    protected Channel_Definition $definition;
    protected array $endpoints;
    protected I_Api_Authenticator $authenticator;

    public function __construct(string $namespace, string $title, string $rest_base, I_Api_Authenticator $authenticator)
    {
        $this->definition = new Channel_Definition($namespace, $title, $rest_base);
        $this->authenticator = $authenticator;
    }

    final protected function register_endpoint(I_Endpoint $endpoint): void
    {
        $this->endpoints[] = $endpoint;
    }

    final public function register(): void
    {
        foreach ($this->endpoints as $endpoint) {
            $endpoint->register();
        }
    }

    final public function get_definition(): Channel_Definition
    {
        return $this->definition;
    }
}
