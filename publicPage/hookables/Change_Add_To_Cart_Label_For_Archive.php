<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;

class Change_Add_To_Cart_Label_For_Archive extends Abstract_Filter_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_product_add_to_cart_text', 'change_add_to_cart_text_for_archive', 10, 2);
    }

    public function change_add_to_cart_text_for_archive(string $default, \WC_Product $product): string
    {

        //TODO: make this an option in the wordpress control panel for easy modification
        switch ($product->get_type()) {
            case 'variable':
            case 'grouped':
            case 'external':
            default:
                return $default;
            case 'simple':
                return $default;
        }
    }
}
