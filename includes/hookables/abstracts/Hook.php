<?php

declare(strict_types=1);

namespace PWP\includes\hookables\abstracts;

/**
 * Hook data structure, component of the Hookable system.
 */
final class Hook
{
    public string $hook;
    public int $priority;

    /**
     * Hook data structure, component of the Hookable system.
     * @param string $hook wordpress hook to observe/listen to
     * @param integer $priority priority of the hook
     */
    public function __construct(string $hook, int $priority)
    {
        $this->hook = $hook;
        $this->priority = $priority;
    }
}
