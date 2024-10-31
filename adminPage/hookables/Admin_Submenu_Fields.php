<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

class Admin_Submenu_Fields extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('admin_init', 'register_submenu_fields');
    }

    public function register_submenu_fields()
    {
        $menus = apply_filters('pwp_get_admin_menu_tabs', []);

        $get = $_GET;
        $activeTab =  isset($get['tab']) ? sanitize_text_field($get['tab']) : '';

        if (isset($menus[$activeTab]) && !empty($menus[$activeTab])) {
            $menu = $menus[$activeTab];
            $menu->register_settings();
        }
    }
}
