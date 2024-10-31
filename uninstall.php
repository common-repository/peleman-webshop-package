<?php

declare(strict_types=1);

namespace PWP\includes;

use wpdb;

defined('ABSPATH') || exit;
defined('WP_UNINSTALL_PLUGIN') || die;

//TODO: cleanup and make OOP maybe?
//clear out local Database table for API keys
global $wpdb;
$table_name = $wpdb->prefix . 'pwp_api_keys';
if (!$wpdb instanceof wpdb) {
    return;
}

dbDelta($wpdb->prepare(
    "DROP TABLE IF EXISTS %s;",
    $table_name
));

//unregister settings
unregister_setting(PWP_OPTION_GROUP, 'pwp-version');