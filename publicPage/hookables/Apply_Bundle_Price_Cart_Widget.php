<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;

class Apply_Bundle_Price_Cart_Widget extends Abstract_Filter_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_widget_cart_item_quantity', 'apply_bundle_pricing', 10, 3);
    }

    public function apply_bundle_pricing(string $price, array $cart_item, string $cart_item_key): string
    {
        $id = $cart_item['variation_id'] ?: $cart_item['product_id'];

        $meta = new Product_Meta_Data(wc_get_product($id));
        if (!$meta->get_unit_amount() > 1 || !$meta->get_unit_price() > 0) {
            return $price;
        }

        $quantity = $cart_item['quantity'];
        $bundlePrice = $meta->get_unit_price();
        ob_start();
?>
        <span class="quantity"> <?php echo esc_html($quantity); ?> &times;
            <span class="?woocommerce-Price-amount amount">
                <bdi>
                    <span class="woocommerce-Price-currencySymbol">
                        <?php echo get_woocommerce_currency_symbol(); ?>
                    </span>
                    <?php echo wc_format_localized_price($bundlePrice); ?>
                </bdi>
            </span>
        </span>
<?php
        return ob_get_clean();
    }
}
