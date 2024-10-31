<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\PIE_Project;
use PWP\includes\editor\Product_PIE_Data;
use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

/**
 * Adds a button in the cart menu for users to return to the image editor to revise or make adjustments
 */
class Display_Editor_Project_Button_In_Cart extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_after_cart_item_name', 'render_project_button', 10, 2);
    }

    public function render_project_button(array $cart_item, string $cart_item_key)
    {
        //get project data from cart item
        if (!isset($cart_item['_project_id']) || !isset($cart_item['_editor_id']))
            return;

        $editor_id = $cart_item['_editor_id'];
        $project_id = $cart_item['_project_id'];
        $project_url = '';

        switch ($editor_id) {
            case (Product_PIE_Data::MY_EDITOR):
                $project_url = $this->get_PIE_Project_Url($cart_item, $project_id);
                break;
            default:
                return;
        }
?>
        <span>
            <p>
                <a href="<?php echo esc_url($project_url); ?>" class="pwp_editor_button"><?php echo esc_html__('Edit your project', 'Peleman-Webshop-Package'); ?></a>
            </p>
        </span>
<?php
    }

    private function get_PIE_Project_Url(array $cart_item, string $project_id): string
    {
        $variant_id = $cart_item['variation_id'];
        $product_id = $cart_item['product_id'];
        $data = new Product_PIE_Data(wc_get_product($variant_id ?: $product_id));
    
        $project = new PIE_Project($data, $project_id);
        return wc_clean($project->get_project_editor_url(true, $this->check_organisation()));
    }

    public function check_organisation(){
        if(isset($_GET['organisationid'])){
            $key = $this->get_organisation_apikey();
        }else{
            $key =  get_option('pie_api_key');
        }
        return $key;
    }
    public function get_organisation_id(){
		if( isset($_GET['organisationid'])){
            return $_GET['organisationid'];
        };
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
}
