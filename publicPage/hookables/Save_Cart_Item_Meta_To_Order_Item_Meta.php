<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Editor_Project;
use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use WC_Order_Item_Product;

/**
 * Saves PDF meta from cart item to order item
 */
class Save_Cart_Item_Meta_To_Order_Item_Meta extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_checkout_create_order_line_item', 'save_cart_item_meta_to_order_item_meta', 10, 4);
    }

    public function save_cart_item_meta_to_order_item_meta(\WC_Order_Item_Product $item, string $cartItemKey, array $values, \WC_Order $order): void
    {
		// error_log('Save cart item meta to order item');
		// error_log(print_r($item, true));
        if (isset($values['_editor_id']) && isset($values['_project_id'])) {

            // $item->add_meta_data(
            //     'Editor Id',
            //     $values['_editor_id'],
            //     true
            // );
            $item->add_meta_data(
                // 'PIE Project ID',
                '_project_id',
                $values['_project_id'],
                true
            );

        }
		if(isset($values['_project_reference'])){
				$item->add_meta_data(
                // '',
                '_project_reference',
                $values['_project_reference'],
                true
            );
		}
    }
}
