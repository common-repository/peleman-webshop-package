<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\hookables\abstracts\I_Hookable_Component;
use PWP\includes\loaders\Plugin_Loader;

/**
 * Adds admin only control panel for editor settings: necessary for connecting to and authenticating with the Peleman Image Editor.
 */
class PIE_Editor_Control_Panel extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('admin_menu', 'pwp_register_control_panel');
    }
    public function pwp_register_control_panel(): void
    {
        add_submenu_page(
            "Peleman_Control_Panel",
            "Editor Settings",
            "Editor Settings",
            "manage_options",
            "Editor_Config",
            array($this, "render_menu"),
            1
        );
    }

    public function render_menu(): void
    {
?>
        <div class="wrap pwp_settings">
            <h1>Editor Settings</h1>
            <hr>
            <div>
                <p>
                    the Peleman Webshop Package has been designed to work in tandem with the Peleman Image Editor (PIE)
                    In order for proper communication with the PIE, they have to be set up to communicate with one another
                </p>
                <p>
                    in this panel, you can enter this webshop's credentials for accessing the PIE API.
                </p>
                <p>
                </p>
            </div>
            <form method="POST" action="options.php">
                <?php
                settings_fields('editorOptions-group');
                do_settings_sections('editorOptions-group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="pie_domain">PIE Domain (URL)</label>
                        </th>
                        <td>
                            <input id="pie_domain" name="pie_domain" type="text" value="<?php echo esc_url(get_option('pie_domain')); ?>" placeholder="https://deveditor.peleman.com" class="regular-text code" />
                            <p class="description" id="tagline-description">base Site Address of the PIE editor</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="pie_customer_id">PIE Customer ID</label>
                        </th>
                        <td>
                            <input id=" pie_customer_id" name="pie_customer_id" type="text" value="<?php echo esc_attr(get_option('pie_customer_id')); ?>" class="regular-text" />
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">
                            <label for="pie_api_key">PIE api key</label>
                        </th>
                        <td>
                            <input id="pie_api_key" name="pie_api_key" type="text" value="<?php echo esc_attr(get_option('pie_api_key')); ?>" class="regular-text" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
<?php
    }
}
