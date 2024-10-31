<?php

declare(strict_types=1);

namespace PWP\includes;

use PWP\includes\hookables\abstracts\I_Hookable_Component;

class Plugin_Loader
{
    /**

     * @var I_Hookable_Component[]
     */
    private array $hookables;

    public function __construct()
    {
        $this->hookables = array();
    }

    /**
     * Add hookable component to list of functions to register.
     * The main purpose of this functionality is to allow the plugin loader to properly time the loading of all hookables,
     * as loading at the wrong time in the wordpress initialization process can cause hooks & hookables to not function properly.
     *
     * @param I_Hookable_Component $hookable
     * @return void
     */
    public function add_hookable(I_Hookable_Component $hookable)
    {
        $this->hookables[] = $hookable;
    }

    /**
     * callback function to add all hooks.
     *
     * @return void
     */
    final public function register_hooks_to_wp(): void
    {
        foreach ($this->hookables as $loadable) {
            $loadable->register();
        }

        $this->hookables = array();
    }
}
