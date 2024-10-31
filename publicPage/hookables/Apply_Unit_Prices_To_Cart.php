<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

class Apply_Unit_Prices_To_Cart extends Abstract_Action_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct(
            'pwp_modify_cart_item_before_calculate_totals',
            'apply_unit_prices',
            $priority,
            1
        );
    }

    public function apply_unit_prices(array $cartItem): void
    {
        /** @var \WC_Product */
        $product = $cartItem['data'];
        $quantity = $cartItem['quantity'];

        $unitAmount = (int)$product->get_meta('unit_amount');
        $unitPrice = (float)$product->get_meta('unit_price');

        //override price with cart unit price
        if ($unitAmount > 1 && $unitPrice > 0) {
            $cartItem['data']->set_price($unitPrice);
        }
    }
    
}
