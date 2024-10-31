<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

/**
 * Enqueue Admin control panel specific .js scripts
 */
class Admin_Enqueue_Scripts extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('admin_enqueue_scripts', 'enqueue_scripts');
    }

    public function enqueue_scripts()
    {
        $randomVersionNumber = wp_rand(0, 1000);
        wp_enqueue_script(
            'pwp_product_menu',
            plugins_url('../js/pwp_product_menu.js', __FILE__),
            array(),
            $randomVersionNumber
        );
    }
}
