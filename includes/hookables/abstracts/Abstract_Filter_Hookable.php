<?php

declare(strict_types=1);

namespace PWP\includes\hookables\abstracts;

/**
 * Abstract observer class for implementing WP filter hooks in an OOP fashion. 
 */
abstract class Abstract_Filter_Hookable extends Abstract_Hookable
{

    final public function register(): void
    {
        foreach ($this->hooks as $hook) {
            \add_filter(
                $hook->hook,
                array($this, $this->callback),
                $hook->priority,
                $this->accepted_args
            );
        }
    }


    final public function deregister(): void
    {
        foreach ($this->hooks as $hook) {
            remove_filter(
                $hook->hook,
                array($this, $this->callback),
                $hook->priority
            );
        }
    }
}
