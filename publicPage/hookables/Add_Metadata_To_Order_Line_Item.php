<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

/**
 * takes meta data from a cart item and pushes it into its order item
 */
class Add_Metadata_To_Order_Line_Item extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_checkout_create_order_line_item', 'add_metadata_to_order_line_item', 30, 4);
    }

    public function add_metadata_to_order_line_item(\WC_Order_Item_Product $item, string $cartItemKey, array $values, \WC_Order $order): void
    {
        //TODO: figure out what the hell the point of this is. 
        // I reckon it's important, but at the same time, how would I know?
        // adding unit information?
        $itemId = $values['variation_id'] ?: $values['product_id'];
        $orderItem = wc_get_product($itemId);
        if (!empty($orderItem->get_meta('cart_price'))) {
            $item->add_meta_data('_cart_price', $orderItem->get_meta('cart_price'), true);
            $item->add_meta_data('_cart_units', $orderItem->get_meta('cart_units'), true);
            $item->add_meta_data('_unit_code', $orderItem->get_meta('unit_code'), true);
        }

        $itemMetaData = $orderItem->get_meta_data();
        $item->add_meta_data('product_meta_data', $itemMetaData, true);
    }
}
