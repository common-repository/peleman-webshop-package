<?php

declare(strict_types=1);

namespace PWP\includes\hookables\abstracts;

/**
 * Abstract observer class for implementing WP sortcodes in an OOP fashion. 
 */
abstract class Abstract_Shortcode_Hookable extends Abstract_Hookable
{
    /**
     * Shortcode tag
     *
     * @var string
     */
    protected string $tag;
    protected string $callback;

    /**
     * Abstract observer class for a WP shortcode
     *
     * @param string $tag shortcode tag
     * @param string $callback name of the method which the shortcode calls.
     */
    public function __construct(string $tag, string $callback)
    {
        $this->tag = $tag;
        $this->callback = $callback;
    }

    final public function register(): void
    {
        \add_shortcode($this->tag, array($this, $this->callback));
    }
}
