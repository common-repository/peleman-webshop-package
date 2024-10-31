<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\menus\Admin_Menu;

/**
 * Adds the primary control panel to the Admin Control panel. Other control panels should be children of the one defined here.
 */
class Admin_Control_Panel extends Abstract_Action_Hookable
{
    public const PAGE_SLUG = 'peleman-control-panel';

    public function __construct()
    {
        parent::__construct('admin_menu', 'pwp_add_control_panel', 9);
    }

    public function pwp_add_control_panel(...$args): void
    {
        add_menu_page(
            __("Peleman Webshop Control Panel", 'Peleman-Webshop-Package'),
            "Peleman Products Extender",
            "manage_options",
            $this::PAGE_SLUG,
            array($this, 'render_tab_buttons'),
            'dashicons-hammer',
            120
        );
    }

    public function render_tab_buttons()
    {
        $get = $_GET;
        $activeTab =  isset($get['tab']) ? sanitize_text_field($get['tab']) : '';
        error_log((string)$activeTab);

        /**
         * @var Admin_Menu[]
         */
        $tabGroups = apply_filters('pwp_get_admin_menu_tabs', []);
?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2>Webshop Settings</h2>
            <?php settings_errors();
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=<?php echo esc_attr($this::PAGE_SLUG); ?>" class="nav-tab <?php echo esc_html($activeTab == '' ? 'nav_tab_active' : ''); ?>">General</a>
                <?php
                foreach ($tabGroups as $key => $group) :
                ?>
                    <a href="<?php esc_html_e($this->assemble_tab_url($key)); ?>" class=" nav-tab <?php esc_html_e($activeTab == $key ? 'nav_tab_active' : ''); ?>"><?php esc_html_e($group->get_title()); ?></a>
                <?php endforeach; ?>
            </h2>

            <!-- <form method="post" action="options.php"> -->
            <form method="post" action='<?php echo esc_url(add_query_arg('tab', $activeTab, admin_url('options.php'))); ?>'>
                <?php
                if (isset($tabGroups[$activeTab]) && !empty($tabGroups[$activeTab])) {
                    $tabGroups[$activeTab]->render_menu($this::PAGE_SLUG);
                } else {
                    $this->display_general_message();
                }
                ?>
            </form>
        </div>
    <?php
    }

    private function display_general_message()
    {
    ?>
        <div class="pwp-settings">
            <h1>Peleman Products Extender</h1>
            <h3>current version: <?php echo esc_html(PWP_VERSION); ?></h3>
            <hr>
            <p>The Peleman Products Extender has been designed to work in tandem with the <b>Peleman Image Editor (PIE)</b></p>
            <p>The PWP plugin requires the following plugins for its functionality:</p>
            <ul>
                <li>Woocommerce 7.2.0+</li>
            </ul>
            <hr>
            <p>For proper communication with the <b>PIE</b>, The plugin will require proper PIE API credentials. Please go to the <b>Editor tab</b> to get started.</p>
        </div>
<?php
    }

    private function assemble_tab_url(string $key): string
    {
        return esc_html(sprintf('?page=%s&tab=%s', $this::PAGE_SLUG, $key));
    }
}
