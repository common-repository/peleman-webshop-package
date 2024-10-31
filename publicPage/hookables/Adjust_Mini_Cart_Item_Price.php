<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\editor\Product_Meta_Data;

class Adjust_Mini_Cart_Item_Price extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_widget_cart_item_quantity', 'update_cart_quantities', 10, 3);
    }

    public function update_cart_quantities(string $price, array $cart_item, string $cart_item_key): string
    {
        $productId = $cart_item['variation_id'] ?: $cart_item['product_id'];
        $meta = new Product_Meta_Data(wc_get_product($productId));

        $unitAmount = $meta->get_unit_amount();
        if ($unitAmount > 1) {
            $price = $meta->get_unit_price();
            ob_start();
?>
            <span class="quantity"> <?php echo esc_html($unitAmount); ?> </span> X
            <span class="woocommerce-Price-amount amount">
                <bdi>
                    <span class="woocommerce-Price-currencySymbol">
                        <?php echo get_woocommerce_currency_symbol(); ?>
                    </span>
                    <?php echo wc_price($price); ?>
                </bdi>
            </span>
<?php

            $output = ob_get_clean();
            return $output;
        }

			return $price;
		}

    }
