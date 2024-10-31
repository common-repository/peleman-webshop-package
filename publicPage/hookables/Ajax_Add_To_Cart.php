<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Editor_Auth_Provider;
use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\editor\Editor_Project;
use PWP\includes\editor\New_PIE_Project_Request;
use PWP\includes\editor\PIE_Editor_Instructions;
use PWP\includes\editor\Product_PIE_Data;
use PWP\includes\editor\PIE_Project;
use PWP\includes\exceptions\Invalid_Response_Exception;
use PWP\includes\hookables\abstracts\Abstract_Ajax_Hookable;

/**
 * AJAX method which handles add to cart requests. If the product being added to the cart requires customization,
 * this hook will also generate a new project entry, store the product temporarily in a session, and redirect the user to the
 * appropriate editor.
 */
class Ajax_Add_To_Cart extends Abstract_Ajax_Hookable
{
	
    public function __construct(int $priority = 10)
    {
        parent::__construct(
            'Ajax_Add_To_Cart',
            plugins_url('../js/pwp-add-to-cart.js', __FILE__),
            $priority
        );
    }

    public function callback(): void
    {
        if (!$this->verify_nonce($_REQUEST['nonce']))
            wp_send_json_error(
                array('message' => __('session timed out', 'Peleman-Webshop-Package')),
                401
            );

        if (!isset($_REQUEST['product_id'])) {
            wp_send_json_error(
                array('message' => __('invalid request', 'Peleman-Webshop_Package')),
                400
            );
        }

        try {
            //find and store all relevant variables
            //first, read and interpret data from request
            $productId      = apply_filters('woocommerce_add_to_cart_product_id', sanitize_key($_REQUEST['product_id']));
            $variationId    = apply_filters('woocommerce_add_to_cart_product_id', sanitize_key($_REQUEST['variation_id'])) ?: 0;
            $product        = wc_get_product($variationId ?: $productId);
            $productMeta    = new Product_Meta_Data($product);
            $quantity       = wc_stock_amount((float)$_REQUEST['quantity'] ?: 1);
            $redirectUrl    = '';
            $projectReference = $_REQUEST['project_reference'];
            //error_log('--1--' . $projectReference);
            $productMeta->set_project_reference($projectReference);
            if (apply_filters('woocommerce_add_to_cart_validation', true, $productId, $quantity, $variationId)) {
                wc_clear_notices();
                //store relevant data in session
                if ($productMeta->is_customizable()) {
                    
                    $this->setup_custom_project($productMeta, (int)$productId, (int)$variationId, $quantity);
                }

                $meta = apply_filters(
                    'pwp_add_cart_item_data',
                    array(),
                    $product,
                    $productMeta,
                );

                $itemKey = WC()->cart->add_to_cart($productId, $quantity, $variationId, array(), $meta);
                if ($itemKey) {
                    do_action('woocommerce_ajax_added_to_cart', $productId);

                    if (boolval(get_option('woocommerce_cart_redirect_after_add'))) {
                        wc_add_to_cart_message(array($productId => $quantity), true);
                        $redirectUrl = wc_get_cart_url();
                    }
                    // wc_ajax::get_refreshed_fragments();

                    wp_send_json_success(array(
                        'message' => __('standard product, using default functionality', 'Peleman-Webshop-Package'),
                        'destination_url' => $redirectUrl ?: '',
                    ), 200);
                }
            }
            $notices = wc_get_notices('error');
            $message = $notices[count($notices) - 1]['notice'];

            wc_clear_notices();
            wp_send_json_error(
                array(
                    'message'   => $message,
                    'data'      => 'data validation error has occurred',
                ),
            );
         
        } catch (Invalid_Response_Exception $err) {
            error_log((string)$err);
            wp_send_json_error(
                array('message' => __('Could not connect to Peleman Image Editor. Please try again in a few moments.', 'Peleman-Webshop-Package')),
            );
        } catch (\Exception $err) {
            error_log(sprintf("PHP Error: %s in %s on line %s", $err->getMessage(), $err->getFile(), $err->getLine()));
            error_log($err->getTraceAsString());

            wp_send_json_error(
                array(
                    'message' => __('The System has encountered an unexpected error. Please try again in a few moments.', 'Peleman-Webshop-Package'),
                ),
                500
            );
        }
    }

    public function callback_nopriv(): void
    {
        $this->callback();
    }

    /**
     * attempt creation of a new project. Method will try to use the template Id to determine what editor is to be used.
     *

     * @param Product_Meta_Data $data template Id of the product. Needed to deterime the appropriate Editor
     * @param string $returnURL url to which the editor will return the user after saving their project, if blank, refer to editor.
     * @param string $cancelURL url to which the editor will return the user if the user cancels their project.
     * @return Editor_Project|null wil return a Editor_Project object if successful. if the method can not determine a valid editor, will return null.
     */
    public function generate_new_editor_Project(Product_Meta_Data $data, string $returnURL = '', string $cancelURL = '', array $params = []): ?Editor_Project
    {
        switch ($data->get_editor_id()) {
            case Product_PIE_Data::MY_EDITOR:
                
                return $this->new_PIE_Project($data->pie_data(), $returnURL ?: site_url(), $params);
            default:
                return null;
        }
    }

    /**
     * generate a new project for the Peleman Image Editor
     *
     * @param Product_PIE_Data $data product or variant id of the product
     * @param string $returnUrl when the user has completed their project, they will be redirected to this URL
     * @return PIE_Project project object
     */
    private function new_PIE_Project(Product_PIE_Data $data, string $returnUrl, array $params): PIE_Project
    {
        $projectReference = $_REQUEST['project_reference'];
        $user = wp_get_current_user();
        $instructions = new PIE_Editor_Instructions($data->get_parent());
        $auth = new Editor_Auth_Provider();
        $request = new New_PIE_Project_Request($auth);
        $request->initialize_from_pie_data($data);
        $request->set_organisation_id($this->get_organisation_id());
        $request->set_organisation_apikey($this->get_organisation_apikey());
        $request->set_return_url($returnUrl);
        $request->set_user_id(get_current_user_id());
        $request->set_user_email($user->user_email);
        $request->set_language($this->get_site_language() ?: 'en');
        $request->set_project_name($projectReference);
        $request->set_timeout(10);
        foreach ($params as $param => $value) {
            $request->add_request_parameter($param, $value);
        }
        return $request->make_request();
    }

    public function get_organisation_id(){
		if(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY) != null){
			parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $queries);
			if(isset($queries['organisationid'])){
				return $queries['organisationid'];
			}elseif(isset($_GET['organisationid'])){
				return $_GET['organisationid'];
			}
    }else{
			return '';
		}
	}

		public function get_organisation_apikey(){
       
		// Build a query and search an organisation with organisation_editor_id
		$query = new \WP_Query( array (
			'post_type'              => array( 'organisation' ),
			'post_status'            => array( 'publish' ),
			'meta_query'             => array(
				array(
					'key'       => '_organisation_editor_id',
					'value'     => $this->get_organisation_id(),
				),
			),
		) );
 
		// Loop over the query and see if there is a post that matches
		if ( $query->have_posts() ) {
 
			// If there is post, return the ID of post
			while ( $query->have_posts() ) {
				$query->the_post();
				return get_post_meta( get_the_ID(), '_organisation_apikey', true );
				break;
			}
		}
	}
    private function get_site_language(): string
    {
        if (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE) {
            return ICL_LANGUAGE_CODE;
        }
        return explode("_", get_locale())[0];
    }

    private function log_upload()
    {
        error_log(__CLASS__ . "\r\nincoming request : " . print_r($_REQUEST, true));
        if (!empty($_FILES)) {
            error_log(__CLASS__ . "\r\nuploaded files : " . print_r($_FILES, true));
        }
    }
    /**
     * Undocumented function
     *
     * @param Product_Meta_Data $productMeta
     * @param integer $productId
     * @param integer $variationId
     * @param integer $quantity
     * @return void
     */
    private function setup_custom_project(Product_Meta_Data $productMeta, int $productId, int $variationId, int $quantity)
    {
        
        /**
         * Filter for adding additional meta data to an item being added to the cart.
         */
        $meta = apply_filters(
            'pwp_add_cart_item_data',
            [],
            $productMeta->get_parent(),
            $productMeta,
        );
        /**
         * Filter for adding additional parameters to the editor create project request.
         */
        $params = apply_filters(
            'pwp_prepare_new_pie_project_params',
            [],
            $productMeta->get_parent(),
            $quantity,
            $meta
        );

        //create a new transient ID for storing our project data
        //this allows us to add the item to the cart when the user returns from the editor and not before.
        $transientId = uniqid('pwpproj-');
        $returnUrl = wc_get_cart_url() . "?CustProj={$transientId}";
        $cancelUrl = get_permalink($variationId ?: $productId);

        $projectData = $this->generate_new_editor_Project($productMeta, $returnUrl, $cancelUrl, $params);

        $meta['_editor_id'] = $projectData->get_editor_id();
        $meta['_project_id'] = $projectData->get_project_id();
		$meta['_project_reference'] = $productMeta->get_project_reference();

        $itemData = array(
            'product_id'    => $productId,
            'quantity'      => $quantity,
            'variation_id'  => $variationId,
            'item_meta'     => $meta,

        );

        //transient expires in 30 days
        set_transient($transientId, $itemData, 30 * 86400);
        wp_send_json_success(
            array(
                'message' => __('external project created, redirecting user to editor for customization...', 'Peleman-Webshop-Package'),
                'destination_url' => $projectData->get_project_editor_url(false,$this->get_organisation_apikey()),
            ),
            201
        );
    }

    protected function object_data(): array
    {
        return array(
            'uploading_text' => __("Uploading file, please wait", 'peleman-webshop-package'),
        );
    }
}
