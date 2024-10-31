<?php

declare(strict_types=1);

namespace PWP;

require plugin_dir_path(__FILE__) . '/vendor/autoload.php';

use PWP\includes\Activator;
use PWP\includes\Deactivator;
use PWP\includes\Plugin;

/**
 * @link              https://www.peleman.com
 * @since             0.1.0
 * @package           PWP
 *
 * @wordpress-plugin
 * Plugin Name:       Peleman Products Extender 
 * Plugin URI:        https://github.com/Peleman-NV/WP_Peleman_Products_Extender
 * requires PHP:      7.4
 * requires at least: 5.9.0
 * Description:       Integrate additional options within Woocommerce product settings to establish connections with the Peleman Image Editor and facilitate the uploading of PDF files.
 * Version:           2.1.0
 * Author:            Peleman Industries
 * Author URI:        https://github.com/Peleman-NV/WP_Peleman_Products_Extender
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       'Peleman Products Extender'
 * Domain Path:       /languages
 */

defined('WPINC') || die;

//define PWP constant values
define('PWP_VERSION', '2.0.1');
!defined('PWP_OPTION_GROUP')        ? define('PWP_OPTION_GROUP', 'OPTIONS') : null;

!defined('PWP_DIRECTORY')           ? define('PWP_DIRECTORY', plugin_dir_path(__FILE__)) : null;
/**@phpstan-ignore-next-line */
!defined('PWP_UPLOAD_DIR')          ? define('PWP_UPLOAD_DIR', WP_CONTENT_DIR . '/uploads/pwp/') : null;
!defined('PWP_TEMPLATES_DIR')       ? define('PWP_TEMPLATES_DIR', plugin_dir_path(__FILE__) . '/templates') : null;
/**@phpstan-ignore-next-line */
!defined('PWP_LOG_DIR')             ? define('PWP_LOG_DIR', WP_CONTENT_DIR . '/uploads/pwp/logs') : null;

!defined('PWP_API_KEY_TABLE')       ? define('PWP_API_KEY_TABLE', 'pwp_api_keys') : null;
!defined('PWP_PROJECTS_TABLE')      ? define('PWP_PROJECTS_TABLE', 'pwp_projects') : null;

/**
 * If the 'is_plugin_active' function is not yet defined, include it
 */
if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/**
 * If the 'WooCommerce' plugin is not active
 * 'wp_die' function can be used to display an error message to the user
 */
if (!is_plugin_active('woocommerce/woocommerce.php')) {
    wp_die('The "WooCommerce" plugin has not been activated. ← <a href="http://localhost/peleman/wordpress/wp-admin/plugins.php")> Please activate it first.</a> ');
}

/**
 * Include the dimention unit file for changing between inch and mm
 * This file passes the dimension units from WooCommerce to the pwp-show-variation.js file.
 */
 include_once(ABSPATH . 'wp-content/plugins/WP_Peleman_Products_Extender/publicPage/hookables/Get_Woocommerce_Dimension_Unit.php');

//register activation hook. Is called when the plugin is activated in the Wordpress Admin panel
register_activation_hook(__FILE__, function () {
    $activator = new Activator();
    $activator->activate();
});

//register deactivation hook. Is called when the plugin is deactivated in the Wordpress Admin panel
register_deactivation_hook(__FILE__, function () {
    $deactivator = new Deactivator();
    $deactivator->deactivate();
});

Plugin::run();
