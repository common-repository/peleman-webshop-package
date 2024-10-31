<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

/**
 * This file passes the dimension units from WooCommerce to the pwp-show-variation.js file.
 */
$handle = 'get-woocommerce-dimension-unit'; // Create a handle to save the JavaScript file

$js_file_path = 'js/pwp-show-variation.js'; // Specify the path to your JavaScript file

wp_enqueue_script($handle, plugin_dir_url(__FILE__) . $js_file_path, array('jquery'), null, true); // Add the JavaScript file to WordPress

$dimension_unit = get_option('woocommerce_dimension_unit'); // Get WooCommerce dimension unit

wp_localize_script($handle, 'dimension_unit', $dimension_unit); // Export the variable to a JavaScript file