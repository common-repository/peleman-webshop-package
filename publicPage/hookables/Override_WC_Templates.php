<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

/** overrides standard Woocommerce templates with PWP versions */
class Override_WC_Templates extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_locate_template', 'override_wc_template', 12, 3);
    }

    public function override_wc_template(string $template, string $templateName, string $templatePath): string
    {
        switch (basename($template, '.php')) {
            case 'meta':
                return trailingslashit(plugin_dir_path(__FILE__)) . '../../templates/woocommerce/meta.php';
            case 'simple':
                return trailingslashit(plugin_dir_path(__FILE__)) . '../../templates/woocommerce/simple.php';
            case 'variation-add-to-cart-button':
                return trailingslashit(plugin_dir_path(__FILE__)) . '../../templates/woocommerce/variation-add-to-cart-button.php';
            case 'price':
                return trailingslashit(plugin_dir_path(__FILE__)) . '../../templates/woocommerce/price.php';
            case 'variation':
                return trailingslashit(plugin_dir_path(__FILE__)) . '../../templates/woocommerce/variation.php';
            case 'order-details-customer':
                return trailingslashit(plugin_dir_path(__FILE__)) . '../../templates/woocommerce/order-details-customer.php';
	  case 'proceed-to-checkout-button':
                return trailingslashit(plugin_dir_path(__FILE__)) . '../../templates/woocommerce/cart/proceed-to-checkout-button.php';
            default:
                return $template;
        }
    }
}