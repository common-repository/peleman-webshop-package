<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use WC_Product_Simple;

/**
 * Adds an additional hidden field to the add to cart button form, for easy access to the product ID value
 */
class Add_Fields_To_Add_To_Cart_Button extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_after_add_to_cart_button', 'add_simple_product_fields');
    }

    public function add_simple_product_fields(): void
    {
        global $product;
        if (!$product instanceof WC_Product_Simple)
            return;

?>
        <input type="hidden" name="add-to-cart" value="<?php echo esc_html(absint($product->get_id())); ?>" />
<?php
    }
}
