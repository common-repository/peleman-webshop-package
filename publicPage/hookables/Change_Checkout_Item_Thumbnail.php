<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;

class Change_Checkout_Item_Thumbnail extends Abstract_Filter_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct('woocommerce_cart_item_name', 'add_product_image_on_checkout', $priority, 3);
    }

    function add_product_image_on_checkout(string $name, array $cart_item, string $cart_item_key)
    {
        /* Return if not checkout page */
        if (!is_checkout()) {
            return $name;
        }

        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

        $thumbnail = $_product->get_image();

        $image = '<div class="ts-product-image" style="width: 100px; height: auto; display: inline-block; padding-right: 20px; vertical-align: middle;">'
            . $thumbnail .
            '</div>';

        /* Prepend image to name and return it */
        return $image . $name;
    }
}
