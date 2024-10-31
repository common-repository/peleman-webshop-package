<?php

declare(strict_types=1);

namespace PWP\includes\menus;

use PWP\adminPage\hookables\Admin_Control_Panel;

class Editor_Submenu extends Admin_Menu
{
    public function __construct(string $page_slug)
    {
        parent::__construct('Editor', 'pwp-editor-options-group', $page_slug);
    }

    public function register_settings(): void
    {
        register_setting($this->option_group, 'pie_domain', array(
            'type' => 'string',
            'description' => 'base Site Address of the PIE editor',
            'sanitize_callback' => 'esc_url_raw',
            'show_in_rest' => false,
            'default' => ''
        ));
        register_setting($this->option_group, 'pie_customer_id', array(
            'type' => 'string',
            'description' => 'customer id for the PIE Editor',
            'sanitize_callback' => 'wp_filter_nohtml_kses',
            'show_in_rest' => false,
            'default' => ''
        ));
        register_setting($this->option_group, 'pie_api_key', array(
            'type' => 'string',
            'description' => 'customer api key for PIE Editor',
            'sanitize_callback' => 'wp_filter_nohtml_kses',
            'show_in_rest' => false,
            'default' => ''
        ));

        register_setting($this->option_group, 'pwp_cleanup_projects', array(
            'type' => 'bool',
            'description' => 'Wether the system should automatically clean up PDF files.',
            'sanitize_callback' => 'esc_url_raw',
            'show_in_rest' => true,
            'default' => false,
        ));

        $this->add_menu_components();
        $this->add_api_test_button($_GET);
    }

    private function add_menu_components(): void
    {
        add_settings_section(
            'pwp_settings_editors',
            __("Editor", 'Peleman-Webshop-Package'),
            null,
            $this->page_slug,
        );
        add_settings_field(
            'pie_domain',
            __("PIE domain (URL)", 'Peleman-Webshop-Package'),
            array($this, 'text_property_callback'),
            $this->page_slug,
            "pwp_settings_editors",
            array(
                'option' => 'pie_domain',
                'placeholder' => "https://deveditor.peleman.com",
                'description' => __("base Site Address of the PIE editor", 'Peleman-Webshop-Package'),
            )
        );
        add_settings_field(
            'pie_customer_id',
            __("PIE Customer ID", 'Peleman-Webshop-Package'),
            array($this, 'text_property_callback'),
            $this->page_slug,
            "pwp_settings_editors",
            array(
                'option' => 'pie_customer_id',
            )
        );
        add_settings_field(
            'pie_api_key',
            __("PIE API key", 'Peleman-Webshop-Package'),
            array($this, 'text_property_callback'),
            $this->page_slug,
            "pwp_settings_editors",
            array(
                'option' => 'pie_api_key',
            )
        );
        add_settings_field(
            'pie_api_test',
            __("PIE API test", 'Peleman-Webshop-Package'),
            array($this, 'add_api_test_button'),
            $this->page_slug,
            "pwp_settings_editors",
            array(
                'id' => 'pie_api_test',
                'type' => 'button',
                'title' => __('test credentials', 'PelemanWebshopPackage')
            )
        );

        add_settings_field(
            'pwp_cleanup_projects',
            __("Automatically delete old PDF files", 'Peleman-Webshop-Package'),
            array($this, 'bool_property_callback'),
            $this->page_slug,
            $this->option_group,
            array('option' => 'pwp_cleanup_projects')
        );
    }

    public function add_api_test_button(array $args): void
    {
        $id = isset($args['id']) ? $args['id'] : '';
        $type = isset($args['type']) ? $args['type'] : 'button';
        $title = isset($args['title']) ? $args['title'] : 'click me';
?>
        <button id="<?php echo $id; ?>" type="<?php echo $type; ?>"><?php echo $title; ?></button>
<?php
    }
}
