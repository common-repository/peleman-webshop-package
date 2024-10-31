<?php

declare(strict_types=1);

namespace PWP\includes\API;

use PWP\includes\authentication\Authenticator;
use PWP\includes\hookables\abstracts\I_Hookable_Component;
use PWP\restApi\v1\GET_PDF_Endpoint;
use PWP\restApi\v1\GET_Project_Thumbnail;

/**
 * overarching class which contains and handles the creation/registering of API Channels
 */
class API_V1_Plugin implements I_Hookable_Component
{
    private string $namespace;

    /**
     * @var I_Hookable_Component[]
     */
    private array $hookables;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
        $authenticator = new Authenticator();

        $this->add_hookable(new GET_PDF_Endpoint($this->namespace));
        $this->add_hookable(new GET_Project_Thumbnail($this->namespace));
    }

    public function register(): void
    {
        foreach ($this->hookables as $hookable) {
            $hookable->register();
        }
    }

    final public function add_hookable(I_Hookable_Component $hookable): void
    {
        $this->hookables[] = $hookable;
    }
}
