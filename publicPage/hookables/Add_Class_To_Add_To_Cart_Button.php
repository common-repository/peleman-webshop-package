<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;

/**
 * Filter hook to add extra classes to an add to cart button in Woocommerce.
 * This only works with a custom add to cart button template which calls the filter
 */
class Add_Class_To_Add_To_Cart_Button extends Abstract_Filter_Hookable
{
    public function __construct()
    {
        parent::__construct('pwp_single_add_to_cart_button_class', 'pwp_add_icon_class_to_add_to_cart_button_args', 10, 2);
    }

    public function pwp_add_icon_class_to_add_to_cart_button_args(string $classes, \WC_Product $product): string
    {
        $meta = new Product_Meta_Data($product);
        $classes .= ' ' . ($meta->is_customizable() ? 'pwp_customizable' : '');
        return $classes;
    }
}
