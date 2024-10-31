<?php

declare(strict_types=1);

namespace PWP\includes;

#region includes

use PWP\templates\Template;
use PWP\includes\API\API_V1_Plugin;
use PWP\includes\hookables\abstracts\I_Hookable_Component;
use PWP\includes\versionControl\VersionController;

use PWP\adminPage\hookables\Add_Cron_Schedules;
use PWP\adminPage\hookables\Admin_Control_Panel;
use PWP\adminPage\hookables\Admin_Notice_Poster;
use PWP\adminPage\hookables\Admin_Enqueue_Styles;
use PWP\adminPage\hookables\Admin_Submenu_Fields;
use PWP\adminPage\hookables\Variable_Product_Custom_Fields;
use PWP\adminPage\hookables\Admin_Enqueue_Scripts;
use PWP\adminPage\hookables\Save_Variable_Product_Custom_Fields;
use PWP\adminPage\hookables\Ajax_Verify_PIE_Editor_Credentials;
use PWP\adminPage\hookables\Add_PIE_Printfile_Download_Button;
use PWP\adminPage\hookables\Change_Product_Thumbnail_In_Order;
use PWP\adminPage\hookables\Display_PDF_Data_After_Order_Item;
use PWP\adminPage\hookables\Get_Admin_Menu_Tabs;
use PWP\adminPage\hookables\Save_Parent_Product_Custom_Fields;
use PWP\adminPage\hookables\Parent_Product_Custom_Fields;
use PWP\adminPage\hookables\PIE_Editor_Control_Panel;

use PWP\publicPage\hookables\Order_Project;
use PWP\publicPage\hookables\Register_PDF_JS_Scripts;
use PWP\publicPage\hookables\Ajax_Add_To_Cart;
use PWP\publicPage\hookables\Validate_PDF_Upload;
use PWP\publicPage\hookables\Get_PDF_Project_Data;
use PWP\publicPage\hookables\Enqueue_Public_Styles;
use PWP\publicPage\hookables\Override_WC_Templates;
use PWP\publicPage\hookables\Add_PDF_Prices_To_Cart;
use PWP\publicPage\hookables\Ajax_Show_Variation;
use PWP\publicPage\hookables\Display_PDF_Upload_Form;
use PWP\publicPage\hookables\Display_Reference_Field;
use PWP\publicPage\hookables\Display_PDF_Data_In_Cart;
use PWP\publicPage\hookables\Add_PDF_Data_To_Cart_Item;
use PWP\publicPage\hookables\Add_Reference_Project_To_Cart_Item;
use PWP\publicPage\hookables\Display_Project_Data_In_Cart;

use PWP\publicPage\hookables\Change_Cart_Item_Thumbnail;
use PWP\publicPage\hookables\Cleanup_Unordered_Projects;
use PWP\publicPage\hookables\Remove_PDF_On_Cart_Deletion;
use PWP\publicPage\hookables\Add_Custom_Project_On_Return;
use PWP\publicPage\hookables\Set_PIE_Project_As_Completed;
use PWP\publicPage\hookables\Apply_Bundle_Price_Cart_Widget;
use PWP\publicPage\hookables\Add_Class_To_Add_To_Cart_Button;
use PWP\publicPage\hookables\Change_Add_To_Cart_Button_Label;
use PWP\publicPage\hookables\Confirm_PIE_Project_On_Checkout;
use PWP\publicPage\hookables\Add_Fields_To_Add_To_Cart_Button;
use PWP\publicPage\hookables\Apply_Unit_Prices_To_Cart;
use PWP\publicPage\hookables\Change_Add_To_Cart_Archive_Button;
use PWP\publicPage\hookables\Change_Checkout_Item_Thumbnail;
use PWP\publicPage\hookables\Display_Editor_Project_Button_In_Cart;
use PWP\publicPage\hookables\Display_PIE_Project_Thumbnail;
use PWP\publicPage\hookables\editor\Set_PIE_Project_Output_Description;
use PWP\publicPage\hookables\Enqueue_PDF_JS_Scripts;
use PWP\publicPage\hookables\Generate_PIE_Edit_URL;
use PWP\publicPage\hookables\Get_PDF_File_From_Project;
use PWP\publicPage\hookables\Modify_Cart_Item_Before_Calculate_Totals;
use PWP\publicPage\hookables\Save_Cart_Item_Meta_To_Order_Item_Meta;
use PWP\publicPage\hookables\Update_PIE_Project_Return_URL;

#endregion

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

defined('ABSPATH') || exit;

final class Plugin
{
    private string $version;
    private string $plugin_name;
    private Plugin_Loader $loader;
    private Admin_Notice_Poster $noticePoster;
    private Template $templateEngine;

    final public static function run()
    {
        $instance = new Plugin();
        $instance->register_components();
        do_action('pwp_plugin_loaded');
    }

    private function __construct()
    {
        $this->version = defined('PWP_VERSION') ? PWP_VERSION : '1.0.00';
        $this->plugin_name = 'Peleman Webshop Package';
        $this->loader = new Plugin_Loader();
        $this->noticePoster = new Admin_Notice_Poster();
        $this->templateEngine = new Template(PWP_TEMPLATES_DIR);

        if (!$this->check_if_requirements_met()) {
            return;
        }

        if (is_admin()) {
            $versionController = new VersionController(PWP_VERSION, (string)get_option('pwp-version'));
            $versionController->try_update();
            $this->admin_hooks();
        }
        $this->public_hooks();
        $this->api_endpoints();
		
		
        /**
         * filter for pie credentials
         */
        add_filter('get_by_credentials', [$this, 'custom_get_pie_data_tag']);
    }

    private function admin_hooks(): void
    {
        $this->add_hookable($this->noticePoster);
        /** cron jobs */
        $this->add_hookable(new Add_Cron_Schedules());
        /** control panel hookables */
        $this->add_hookables(
            new Admin_Enqueue_Styles(),
            new Admin_Enqueue_Scripts(),
            new Admin_Submenu_Fields(),
            new Admin_Control_Panel(),
            new Get_Admin_Menu_Tabs(),
            new PIE_Editor_Control_Panel(),
        );

        /* product page hookables */
        $this->add_hookables(
            new Parent_Product_Custom_Fields(),
            new Variable_Product_Custom_Fields(),
            new Save_Parent_Product_Custom_Fields(),
            new Save_Variable_Product_Custom_Fields(),
            new Add_PIE_Printfile_Download_Button(),
        );

        $this->add_hookables();
    }

    private function public_hooks(): void
    {
        /* ADDITIONAL ACTIONS & FILTERS */
        $this->add_hookables(
            new Get_PDF_Project_Data(),
            new Update_PIE_Project_Return_URL(),
            new Set_PIE_Project_Output_Description(),
            new Generate_PIE_Edit_URL(),
            new Get_PDF_File_From_Project()
        );

        $this->add_hookables(
            new Ajax_Verify_PIE_Editor_Credentials(6),
            new Cleanup_Unordered_Projects(),
            new Override_WC_Templates(),
            new Change_Checkout_Item_Thumbnail(),
            new Change_Product_Thumbnail_In_Order(10),
        );

        $this->add_hookables(
            new Enqueue_Public_Styles(),
            new Register_PDF_JS_Scripts(),
            new Enqueue_PDF_JS_Scripts(),
        );

        $this->add_hookables(
            new Add_Class_To_Add_To_Cart_Button(),
            new Change_Add_To_Cart_Archive_Button(),
            new Change_Add_To_Cart_Button_Label(),
            new Add_Fields_To_Add_To_Cart_Button(),

            new Set_PIE_Project_As_Completed()
        );


        /* EDITOR product hookables */
        $this->add_hookables(
            new Ajax_Show_Variation(8),
            new Display_Editor_Project_Button_In_Cart(),
            new Add_Custom_Project_On_Return(),
            new Save_Cart_Item_Meta_To_Order_Item_Meta(),
            new Confirm_PIE_Project_On_Checkout(),
        );

        /* CUSTOM ADD TO CART/STOREFRONT OVERRIDES */
        if (get_option('pwp_use_custom_add_to_cart_js', true)) {
            $this->add_hookables(
                new Ajax_Add_To_Cart(8),
                new Apply_Bundle_Price_Cart_Widget(),
                new Modify_Cart_Item_Before_Calculate_Totals(),
                new Apply_Unit_Prices_To_Cart(9),
                new Add_PDF_Prices_To_Cart(20),
                new Add_Reference_Project_To_Cart_Item(),
                new Display_Project_Data_In_Cart()
            );
            /* PDF upload hookables */
            $this->add_hookables(
                new Display_PDF_Upload_Form($this->templateEngine),
                new Validate_PDF_Upload(),
                new Add_PDF_Data_To_Cart_Item(),
                new Display_PDF_Data_In_Cart(),
                new Remove_PDF_On_Cart_Deletion(),
                new Order_Project(),
                new Display_PDF_Data_After_Order_Item()
            );
        }

        /* EDITOR front end display hookables */
        $this->add_hookable(new Change_Cart_Item_Thumbnail());

        $this->add_hookables(
            new Display_PIE_Project_Thumbnail(),
            new Display_Reference_Field(),

        );
    }

    private function api_endpoints(): void
    {
        $this->add_hookable(new API_V1_Plugin('pwp/v1'));
    }

    private function register_components()
    {
        $this->loader->register_hooks_to_wp();
    }

    private function check_if_requirements_met(): bool
    {
        if (!\is_plugin_active('woocommerce/woocommerce.php')) {
            $this->noticePoster->new_warning_notice("{$this->plugin_name} needs Woocommerce to function properly!", true);
        }
        return true;
    }

    private function add_hookable(I_Hookable_Component $hookable): void
    {
        $this->loader->add_hookable(($hookable));
    }

    /**
     * @param I_Hookable_Component ...$hookables
     * @return void
     */
    private function add_hookables(...$hookables): void
    {
        foreach ($hookables as $hookable) {
            $this->loader->add_hookable($hookable);
        }
    }
	
	    /**
         * code snippets for trigger the function.
         * 
         * getting credentials 
         * 
         * $new_pie_data = apply_filters('get_by_credentials', []);
         *   echo '<pre>';
         *   var_dump($new_pie_data);
         *   echo '</pre>';
         * __________________________________________________________________________
         * 
         * getting credentials by their name.
         * just change the $desired_title value to which value's title to see.
         * 
         * $new_pie_data = apply_filters('get_by_credentials', []);
         * $desired_title = 'pie_api_key';
         * $desired_value = isset($new_pie_data[$desired_title]) ? $new_pie_data[$desired_title] : null;
         * echo $desired_value;
         */
    public function custom_get_pie_data_tag($data) 
    {        
        $pie_domain = get_option('pie_domain');
        $pie_customer_id = get_option('pie_customer_id');
        $pie_api_key = get_option('pie_api_key');
    
        $new_data = [
            'pie_domain' => $pie_domain,
            'pie_customer_id' => $pie_customer_id,
            'pie_api_key' => $pie_api_key,
        ];
    
        return $new_data;

        
    }
}
