<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

/**
 * Enqueus public style sheets
 */
class Enqueue_Public_Styles extends Abstract_Action_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct('wp_enqueue_scripts', 'enqueue_public_styles', $priority);
    }

    public function enqueue_public_styles(): void
    {
        $plugin = 'WP_Peleman_Products_Extender';

        wp_enqueue_style(
            'pwp_style',
            plugins_url($plugin . '/publicPage/css/product-page-style.css'),
            array(),
            (string)wp_rand(0, 2000),
            'all'
        );
		wp_enqueue_style('dashicons');
    }
}
