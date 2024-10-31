<?php

declare(strict_types=1);

namespace PWP\includes\hookables\abstracts;

abstract class Abstract_Hookable implements I_Hookable_Component
{
    /**
     * Array of hooks to which this object subscribes/listens, along with the priority value of each hook.
     * 
     * @var Hook[]
     */
    protected array $hooks;
    /**
     * name of the method which this object will call when one of its subscribed hooks is called.
     * @var string
     */
    protected string $callback;
    /**
     * Undocumented variable
     *
     * @var integer
     */
    protected int $accepted_args;

    /**
     * @param string $hook WP Hook to hook onto
     * @param string $callback name of the method that will be called.
     * @param integer $priority execution priority of this component
     * @param integer $accepted_args amount of arguments this method's callback accepts
     */
    public function __construct(string $hook, string $callback, int $priority = 10, $accepted_args = 1)
    {
        $this->hooks[] = new Hook($hook, $priority);
        $this->callback = $callback;
        $this->accepted_args = $accepted_args;
    }

    abstract public function register(): void;
    abstract public function deregister(): void;

    /**
     * Utility method to add additional hooks to listen for. 
     *
     * @param string $hook
     * @param integer $priority
     * @return void
     */
    public function add_hook(string $hook, int $priority = 10): void
    {
        $this->hooks[] = new Hook($hook, $priority);
    }
}
