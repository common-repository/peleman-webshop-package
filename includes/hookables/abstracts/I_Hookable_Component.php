<?php

declare(strict_types=1);

namespace PWP\includes\hookables\abstracts;

use PWP\includes\loaders\Plugin_Loader;

/**
 * interface for objects to register hooks, actions and filters.
 * Hookables are intended as OOP implementations of WP action and filter listeners. 
 */
interface I_Hookable_Component
{
    /**
     * register class hookables with wordpress
     */
    public function register(): void;
}
