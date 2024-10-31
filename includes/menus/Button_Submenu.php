<?php

declare(strict_types=1);

namespace PWP\includes\menus;

use PWP\adminPage\hookables\Admin_Control_Panel;

class Button_Submenu extends Admin_Menu
{
    public function __construct(string $page_slug)
    {
        parent::__construct('Buttons', 'pwp-button-options-group', $page_slug);
    }

    public function register_settings(): void
    {
        register_setting($this->option_group, 'pwp_customize_label', array(
            'type' => 'string',
            'description' => 'label for shop archive; to be displayed on products that require user customization/uploads',
            'sanitize_callback' => 'wp_filter_nohtml_kses',
            'show_in_rest' => true,
            'default' => 'Customize product',
        ));
        register_setting($this->option_group, 'pwp_archive_var_label', array(
            'type' => 'string',
            'description' => 'label for shop archive; to be displayed on variable products that require user customization/uploads',
            'sanitize_callback' => 'wp_filter_nohtml_kses',
            'show_in_rest' => true,
            'default' => 'Choose Options',
        ));
        register_setting($this->option_group, 'pwp_project_cleanup_cutoff_days', array(
            'type' => 'string',
            'description' => 'amount of days before an uploaded pdf project is removed. Only pdfs which have not been ordered will be deleted.',
            'sanitize_callback' => 'sanitize_key',
            'show_in_rest' => false,
            'default' => 15,
        ));

        register_setting($this->option_group, 'pwp_global_pdf_size_validation', array(
            'type' => 'boolean',
            'description' => 'whether the PDF upload system should validate PDF size or not, globally. This will override individual product or variation settings.',
            'show_in_rest'  => true,
            'default' => true,
        ));

        register_setting($this->option_group, 'pwp_use_custom_add_to_cart_js', array(
            'type' => 'boolean',
            'description' => 'Whether to use the default woocommerce add to cart logic & scripts, or the PWP overrides.',
            'show_in_rest'  => true,
            'default' => true,
        ));


        $this->add_menu_components();
    }

    public function add_menu_components(): void
    {
        add_settings_section(
            'pwp_settings_buttons',
            __("Buttons", 'Peleman-Webshop-Package'),
            array($this, 'do_nothing'),
            $this->page_slug,
        );
        add_settings_field(
            'pwp_customize_label',
            __("Simple product - customizable", 'Peleman-Webshop-Package'),
            array($this, 'text_property_callback'),
            $this->page_slug,
            "pwp_settings_buttons",
            array(
                'option' => 'pwp_customize_label',
                'placeholder' => 'customize me',
                'description' =>  __("label for products that require customization/user input", 'Peleman-Webshop-Package'),
            )
        );
        add_settings_field(
            'pwp_archive_var_label',
            __("Variable product - customizable", 'Peleman-Webshop-Package'),
            array($this, 'text_property_callback'),
            $this->page_slug,
            "pwp_settings_buttons",
            array(
                'option' => 'pwp_archive_var_label',
                'placeholder' => 'customize me',
                'description' =>  __("label for customizable variable products", 'Peleman-Webshop-Package')
            )
        );

        add_settings_field(
            'pwp_global_pdf_size_validation',
            __('Global PDF size validation', 'Peleman-Webshop-Package'),
            array($this, 'bool_property_callback'),
            $this->page_slug,
            'pwp_settings_buttons',
            array(
                'option' => 'pwp_global_pdf_size_validation',
                'description' => __("Disabling this will globally override all PDF size validation checks.", 'Peleman-Webshop-Package'),
            )
        );
        add_settings_field(
            'pwp_use_custom_add_to_cart_js',
            __('Use PWP add to cart scripts', 'Peleman-Webshop-Package'),
            array($this, 'bool_property_callback'),
            $this->page_slug,
            'pwp_settings_buttons',
            array(
                'option' => 'pwp_use_custom_add_to_cart_js',
                'description' => __("Whether to use the default woocommerce add to cart logic & scripts, or the PWP overrides.", 'Peleman-Webshop-Package'),
            )
        );
    }

    public function do_nothing(): void
    {
    }
}
