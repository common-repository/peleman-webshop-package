<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;
use PWP\includes\menus\Button_Submenu;
use PWP\includes\menus\Editor_Submenu;
use PWP\includes\menus\F2D_Menu;

class Get_Admin_Menu_Tabs extends Abstract_Filter_Hookable
{
    private array $submenus;
    public function __construct(int $priority = 10)
    {
        parent::__construct('pwp_get_admin_menu_tabs', 'get_pwp_admin_submenus', $priority, 1);
        $this->submenus = [];
    }

    public function get_pwp_admin_submenus(array $submenus)
    {
        if (empty($this->submenus)) {
            $this->submenus = [
                'buttons'   => new Button_Submenu(Admin_Control_Panel::PAGE_SLUG),
                'editor'    => new Editor_Submenu(Admin_Control_Panel::PAGE_SLUG),
                'f2d_menu'  => new F2D_Menu(Admin_Control_Panel::PAGE_SLUG),
            ];
        }

        $submenus += $this->submenus;

        return $submenus;
    }
}
