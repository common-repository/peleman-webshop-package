<?php
/**
 * Proceed to checkout button
 *
 * Contains the markup for the proceed to checkout button on the cart.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/proceed-to-checkout-button.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$cart = WC()->cart;
$cart_items = $cart->get_cart();

$products_list = "Cart Items:\n";

$min_quantity_error = false; // No min_quantity error at startup.
$error_products = array(); // Create an array to store products that fail.
// error_log(print_r($cart_items, true));

foreach ($cart_items as $cart_item_key => $cart_item) {
    $product_name = $cart_item['data']->get_name();
    $product_quantity = $cart_item['quantity'];
    $min_quantity = $cart_item['data']->min_quantity;
	$size = $cart_item['data']->attribute_summary;
    $products_list .= "- $product_name x $product_quantity x $min_quantity\n";

    // If min_quantity is higher than product_quantity, set the error flag and add the products causing the error to the list.
    if ($min_quantity > $product_quantity) {
        $min_quantity_error = true;
        $error_products[] = array(
            'name' => $product_name,
			'size' => $size,
            'min_quantity' => $min_quantity

        );
	    error_log(print_r($error_products, true));

    }
}

?>

<?php if ($min_quantity_error): ?>

    <?php remove_action( 'woocommerce_widget_shopping_cart_before_buttons', 'woocommerce_widget_shopping_cart_subtotal', 10 ); ?>
    <?php remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 ); ?>
    <?php remove_action( 'woocommerce_widget_shopping_cart_after_buttons', 'woocommerce_widget_shopping_cart_cross_sell_display', 10 ); ?>

    <div>
        <p>Please edit the minimum order quantity for the following products:</p> 
		<button style='background-color: #111; border-color: #777; color: #777' onclick="proceedToCheckout()" class="checkout-button button alt wc-forward<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" <?php if ($min_quantity_error): ?>disabled<?php endif; ?>>
			<?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
		</button>
        <table class="table" >
			<thead >
				<tr>
					<th>Product Name</th>
					<th style='text-align: center;'>Min Order Quantity</th>
				</tr>
			</thead>
			<tbody">
				<?php foreach ($error_products as $error_product): ?>
					<tr>
 						<td><?php echo $error_product['name'] . " - " . $error_product['size']; ?></td>
						<td style='text-align: center;'><?php echo $error_product['min_quantity']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	
		

    </div>
<?php else: ?>
    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" onclick="return proceedToCheckout()">
        <?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
    </a>
<?php endif; ?>