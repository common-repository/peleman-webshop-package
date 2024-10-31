<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use WC_Cart;
use WC_Product_Variation;

/**
 * Hook to capture a customer returning from the PIE editor via return Url.
 * If the `CustProj` parameter is set in the request, this hook will add a product stored in
 * session to the cart.
 */
class Add_Custom_Project_On_Return extends Abstract_Action_Hookable
{
    //this is a rather obscure hook, which is called even after wp_loaded.
    //for some reason, wp_loaded is still too early for this method to be called, but wp is the right timing.
    //TODO: perhaps in the future it might be a better idea to make this an API call, that redirects to the cart.
    public function __construct(string $hook = 'wp', int $priority = 10)
    {
        parent::__construct($hook, 'add_customized_product_to_cart', $priority);
    }

    public function add_customized_product_to_cart()
    {
        $get = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!empty($get['CustProj'])) {
            session_start();

            $transientId = sanitize_key($get['CustProj']);
            $data = get_transient($transientId);
			
			
            if (false !== $data) {
                 //error_log("adding project to cart: " . print_r($data, true));

                $productId      = (int)$data['product_id'];
                $variationId    = (int)$data['variation_id'] ?: 0;
                $quantity       = (int)$data['quantity'] ?: 1;
                $product        = wc_get_product($variationId ?: $productId);
                $variationArr   = [];
                $meta           = $data['item_meta'];
				$location = get_option('pie_domain', '');
                $location .= '/editor/api/projectfileAPI.php?action=get&projectid=' . $meta['_project_id'] . 
						'&a=' . get_option('pie_api_key');
				$response = wp_remote_request(
                     $location,
                 );
				$projectJson = json_decode($response['body']);
			
				$meta['_project_reference'] = $projectJson->name;


                if ($product instanceof WC_Product_Variation) {
                    $variationArr = wc_get_product_variation_attributes($variationId);
                }

                if (!WC()->cart->add_to_cart(
                    $productId,
                    $quantity,
                    $variationId,
                    $variationArr,
                    $meta
                )) {
                    wp_die("something went catastrophically wrong adding the item and project to the cart.");
                }
                wc_add_to_cart_message(array($productId => $quantity), true);
                delete_transient($transientId);
            }
            $organisation = '';
            if (!empty($get['organisationid'])) {
                $organisation = '?organisationid=' . $get['organisationid'];
            }
            wp_redirect(wc_get_cart_url() . $organisation);
            exit;
        }
    }
}
