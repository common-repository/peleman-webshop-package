<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

class Modify_Cart_Item_Before_Calculate_Totals extends Abstract_Action_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct('woocommerce_before_calculate_totals', 'parse_cart_items', $priority, 1);
    }

    public function parse_cart_items(\WC_Cart $cart): void
    {
        foreach ($cart->get_cart() as $cartItem) {
            do_action('pwp_modify_cart_item_before_calculate_totals', $cartItem);
        }

        foreach ($cart->get_fees() as $fee) {
            do_action('pwp_modify_cart_fee_before_calculate_totals', $fee);
        }
    }
}
