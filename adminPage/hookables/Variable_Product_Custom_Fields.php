<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\editor\Product_PIE_Data;
use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\utilities\HTML_Builder;
use WP_Post;


/**
 * Adds PWP/PIE specific fields to a WC Variant product
 */
class Variable_Product_Custom_Fields extends Abstract_Action_Hookable
{
    private int $loop;
    private string $loopSuffix = '';

    public function __construct()
    {
        parent::__construct('woocommerce_product_after_variable_attributes', 'render_custom_fields', 11, 3);
    }

    /**
     * Undocumented function
     *
     * @param int $loop
     * @param array $variation_data
     * @param WP_Post $variation
     * @return void
     */
    public function render_custom_fields(int $loop, array $variation_data, WP_Post $variation): void
    {
        $variationId = $variation->ID;
        $wc_variation = wc_get_product($variationId);
        $meta_data = new Product_Meta_Data(wc_get_product($variationId));
        $parentId = $wc_variation->get_parent_id();

        $this->loop = $loop;
        $this->loopSuffix = "[{$loop}]";

        HTML_Builder::heading(
            __('Product Settings', 'Peleman-Webshop-Package'),
            2,
            ['pwp-options-group-title']
        );
        HTML_Builder::open_div(['classes' => ['pwp-options-group']]);
        $this->render_standard_product_settings($meta_data);
        HTML_Builder::close_div();

        HTML_Builder::heading(
            __('Image Editor Settings', 'Peleman-Webshop-Package'),
            2,
            ['pwp-options-group-title']
        );
        HTML_Builder::open_div(['classes' => ['pwp-options-group']]);
        $this->render_PIE_product_settings($meta_data);
        HTML_Builder::close_div();
        HTML_Builder::heading(
            __('PDF Upload Settings', 'Peleman-Webshop-Package'),
            2,
            ['pwp-options-group-title']
        );
        HTML_Builder::open_div(['classes' => ['pwp-options-group']]);
        $this->render_PDF_upload_settings($meta_data);
        HTML_Builder::close_div();
?>
    <?php
    }

    private function render_standard_product_settings(Product_Meta_Data $meta): void
    {
        if (get_option('pwp_enable_f2d')) {
            woocommerce_wp_text_input(array(
                'id'            => $this->format_loop_id("f2d_sku_components"),
                'name'          =>  $this->format_loop_id("f2d_sku_components"),
                'label'         => __('Fly2Data SKU', 'Peleman-Webshop-Package'),
                'value'         => $meta->get_parent()->get_meta('f2d_sku_components'),
                'desc_tip'      => true,
                'description'   =>  __('F2D components that make up a variation', 'Peleman-Webshop-Package'),
                'wrapper_class' => 'form-row form-row-first pwp-form-row-padding-5',
            ));

            woocommerce_wp_text_input(array(
                'id'            => $this->format_loop_id(Product_meta_data::F2D_ARTICLE_CODE),
                'name'          => $this->format_loop_id(Product_meta_data::F2D_ARTICLE_CODE),
                'label'         => __('Fly2Data article code', 'Peleman-Webshop-Package'),
                'value'         => $meta->get_f2d_article_code() ?: '',
                'desc_tip'      => true,
                'description'   =>  __('Fly2Data article code for this variation/product', 'Peleman-Webshop-Package'),
                'wrapper_class' => 'form-row form-row-last pwp-form-row-padding-5',
                'placeholder'   => 'Fly2Data article code',
            ));
        }
        woocommerce_wp_text_input(array(
            'id'            => $this->format_loop_id(Product_Meta_Data::UNIT_PRICE),
            'name'          => $this->format_loop_id(Product_Meta_Data::UNIT_PRICE),
            'label'         => __('Unit Purchase Price', 'Peleman-Webshop-Package'),
            'value'         =>  (string)$meta->get_unit_price() ?: 0,
            'desc_tip'      => true,
            'description'   => __('The price of the unit total that will be added to cart. This is used in conjunction with UNIT AMOUNT.', 'Peleman-Webshop-Package'),
            'wrapper_class' => 'form-row form-row-first pwp-form-row-padding-5',
            'class'         => "wc_input_price",
            'data_type'     => 'price',
            // 'type' => 'number',
            // 'custom_attributes' => array('step' => 0.01),
            'placeholder'   => wc_format_localized_decimal('0.00'),
        ));

        woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::UNIT_AMOUNT),
            'name'              => $this->format_loop_id(Product_Meta_Data::UNIT_AMOUNT),
            'label'             => __('Unit amount', 'Peleman-Webshop-Package'),
            'value'             => (string)$meta->get_unit_amount() ?: 1,
            'desc_tip'          => true,
            'description'       =>  __('Amount of items per unit. ie. 1 box (unit) contains 20 cards (items).', 'Peleman-Webshop-Package'),
            'wrapper_class'     => 'form-row form-row-last pwp-form-row-padding-5',
            'type'              => 'number',
            'custom_attributes' => array(
                'step'              => 1,
                'min'               => 1
            ),
            'placeholder'       => 1
        ));

        woocommerce_wp_text_input(array(
            'id'            => $this->format_loop_id(Product_Meta_Data::UNIT_CODE),
            'name'          => $this->format_loop_id(Product_Meta_Data::UNIT_CODE),
            'label'         => __('Unit code', 'Peleman-Webshop-Package'),
            'value'         => (string)$meta->get_unit_code(),
            'desc_tip'      => true,
            'description'   =>  __('The unit code for internal identification , ie. BOX, CRT, ...', 'Peleman-Webshop-Package'),
            'wrapper_class' => 'form-row form-row-first pwp-form-row-padding-5',
            'placeholder'   => 'BOX, CRT, ...'
        ));

        woocommerce_wp_text_input(array(
            'id'            => $this->format_loop_id(Product_Meta_Data::CUSTOM_LABEL_KEY),
            'name'          => $this->format_loop_id(Product_Meta_Data::CUSTOM_LABEL_KEY),
            'label'         => __('Custom add to cart label', 'Peleman-Webshop-Package'),
            'value'         => $meta->get_custom_add_to_cart_label(),
            'desc_tip'      => true,
            'description'   =>  __('Custom Add To Cart label that will be displayed on the product page', 'Peleman-Webshop-Package'),
            'wrapper_class' => 'form-row form-row-last pwp-form-row-padding-5',
            'placeholder'   => 'Add to cart'
        ));
		
		woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::MIN_QUANTITY),
            'name'              => $this->format_loop_id(Product_Meta_Data::MIN_QUANTITY),
            'label'             => __('Minimum Quantity', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_min_quantity(),
            'desc_tip'          => true,
            'description'       =>  __('Set the minimum quantity of the order', 'Peleman-Webshop-Package'),
            'wrapper_class'     => 'form-row form-row-first pwp-form-row-padding-5',
            'type'              => 'number', 
			'custom_attributes' => array(
                'step'              => 1,
                'min'               => 1
            ),
            'placeholder'       => 1
        ));		
		
		woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::INCREMENT_STEP),
            'name'              => $this->format_loop_id(Product_Meta_Data::INCREMENT_STEP),
            'label'             => __('increment step', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_increment_step(),
            'desc_tip'          => true,
            'description'       =>  __('Set the steps of the increment', 'Peleman-Webshop-Package'),
            'wrapper_class'     => 'form-row form-row-last pwp-form-row-padding-5',
            'type'              => 'number', 
			'custom_attributes' => array(
                'step'              => 1,
                'min'               => 1
            ),
            'placeholder'       => 1
        ));	
		
         woocommerce_wp_checkbox(array(
             'id'                => $this->format_loop_id(Product_Meta_Data::USE_PROJECT_REFERENCE),
             'name'              =>  $this->format_loop_id(Product_Meta_Data::USE_PROJECT_REFERENCE),
             'label'             => __('Use Reference Field', 'Peleman-Webshop-Package'),
             'value'             => $meta->get_use_project_reference() ? 'yes' : 'no',
            'desc_tip'          => true,
             'description'       => __('Enable/disable reference for this product/variation', 'Peleman-Webshop-Package'),
            'wrapper_class'     => 'form-row form-row-first pwp-form-row-padding-5',
    //         'custom_attributes' => array('foldout' => $editorToggle, 'requires' => $required)
         ));	
    }

    private function render_PIE_product_settings(Product_Meta_Data $meta): void
    {
        $editorToggle = 'editor_' . $this->loop;
        $uploadToggle = 'upload_' . $this->loop;
        $required = 'pie_req_' . $this->loop;

        woocommerce_wp_select(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::EDITOR_ID_KEY),
            'name'              => $this->format_loop_id(Product_Meta_Data::EDITOR_ID_KEY),
            'label'             => __("Editor", 'Peleman-Webshop-Package'),
            'desc_tip'          => true,
            'description'       => __('Enable/disable the editor for this product/variation. Ensure the template ID is at least filled in.', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_editor_id() ?: 'none',
            'wrapper_class'     => 'form-row form-row-full pwp-form-row-padding-5',
            'custom_attributes' => array(
                'foldout'           => $editorToggle,
                'requires'          => $required
            ),
            'options'           => [
                ''                          => 'No customization',
                Product_PIE_Data::MY_EDITOR => 'Peleman Image Editor'
            ],
        ));

        HTML_Builder::open_div([
            'id'            => $editorToggle,
            'classes'       => $meta->get_editor_id() == 'PIE' ? [] : ['pwp-hidden']
        ]);	
		
	woocommerce_wp_text_input(array(
		'id'            => $this->format_loop_id(Product_Meta_Data::PAGE_AMOUNT_KEY),
		'name'          => $this->format_loop_id(Product_Meta_Data::PAGE_AMOUNT_KEY),
		'label'         => __('Default number of pages', 'Peleman-Webshop-Package'),
		'value'         => $meta->get_page_amount(),
        'desc_tip'          => true,
		'description'   => __('Base number of pages for project. Image Editor will start with that amount of pages', 'Peleman-Webshop-Package'),
        'wrapper_class'     => 'form-row form-row-first pwp-form-row-padding-5',
		'type'          => 'number',
		'custom_attributes' => array(
			'step'  => 1,
			'min'   => 0,
		)
	));

		
	woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::COVER_PRICE_PER_PAGE_KEY),
            'name'              => $this->format_loop_id(Product_Meta_Data::COVER_PRICE_PER_PAGE_KEY),
            'label'             => __('Additional price per page', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_cover_price_per_page(),
            'desc_tip'          => true,
            'description'       => __('Additional price per page that will be added to product/variation price', 'Peleman-Webshop-Package'),
            'class'             => "{$required}",
            'wrapper_class'     => 'form-row form-row-last pwp-form-row-padding-5',
            'data_type'         => 'price',
            'placeholder' 		=> wc_format_localized_decimal(0.00),
        ));

        woocommerce_wp_text_input(array(
            'id'            => $this->format_loop_id(Product_PIE_Data::PIE_TEMPLATE_ID_KEY),
            'name'          => $this->format_loop_id(Product_PIE_Data::PIE_TEMPLATE_ID_KEY),
            'label'         => __('Template ID', 'Peleman-Webshop-Package'),
            'value'         => $meta->pie_data()->get_template_id(),
            'desc_tip'      => true,
            'description'   =>  __('ID of the template that will be used in the editor. This needs to correspond with the template ID defined in the editor dashboard', 'Peleman-Webshop-Package'),
            'class'         => $required,
            'wrapper_class' => 'form-row form-row-first pwp-form-row-padding-5',
            'placeholder'   => 'REQUIRED'
        ));

        woocommerce_wp_text_input(array(
            'id'            => $this->format_loop_id(Product_PIE_Data::DESIGN_ID_KEY),
            'name'          => $this->format_loop_id(Product_PIE_Data::DESIGN_ID_KEY),
            'label'         => __('Design ID', 'Peleman-Webshop-Package'),
            'value'         => $meta->pie_data()->get_design_id(),
            'desc_tip'      => true,
            'description'   =>  __('The design theme that can be used in the webshop, ie. Funeral, Copyshop, ...', 'Peleman-Webshop-Package'),
            'wrapper_class' => 'form-row form-row-last pwp-form-row-padding-5',
            'placeholder'   => 'Design ID'
        ));

        woocommerce_wp_text_input(array(
            'id'            => $this->format_loop_id(Product_PIE_Data::BACKGROUND_ID_KEY),
            'name'          => $this->format_loop_id(Product_PIE_Data::BACKGROUND_ID_KEY),
            'label'         => __('Background ID', 'Peleman-Webshop-Package'),
            'value'         => $meta->pie_data()->get_background_id(),
            'desc_tip'      => true,
            'description'   =>  __('The background that will be displayed in the editor. This needs to correspond with the background ID defined in the format', 'Peleman-Webshop-Package'),
            'wrapper_class' => 'form-row form-row-first pwp-form-row-padding-5',
            'placeholder'   => 'Background ID'
        ));

        // woocommerce_wp_text_input(array(
        //     'id' => Product_PIE_Data::FORMAT_ID_KEY,
        //     'name' => Product_PIE_Data::FORMAT_ID_KEY,
        //     'label' => __('Format ID', 'Peleman-Webshop-Package'),
        //     'value' => $meta->pie_data()->get_format_id(),
        //     'desc_tip' => true,
        //     'description' =>  __('format id for the template to be filled out', 'Peleman-Webshop-Package'),
        //     'wrapper_class' => 'form-row form-row-last pwp-form-row-padding-5',
        // ));

        woocommerce_wp_text_input(array(
            'id'            => $this->format_loop_id(Product_PIE_Data::COLOR_CODE_KEY),
            'name'          => $this->format_loop_id(Product_PIE_Data::COLOR_CODE_KEY),
            'label'         => __('Color code', 'Peleman-Webshop-Package'),
            'value'         => $meta->pie_data()->get_color_code(),
            'desc_tip'      => true,
            'description'   =>  __('The color code of this product/variation to use the corresponding background inside the editor. This needs to correspond with the color code defined in the format', 'Peleman-Webshop-Package'),
            'wrapper_class' => 'form-row form-row-last pwp-form-row-padding-5',
            'placeholder'   => 'Color code'
        ));

        woocommerce_wp_checkbox(array(
            'id'                => $this->format_loop_id(Product_PIE_Data::USE_IMAGE_UPLOAD_KEY),
            'name'              =>  $this->format_loop_id(Product_PIE_Data::USE_IMAGE_UPLOAD_KEY),
            'label'             => __('Use Image Uploads', 'Peleman-Webshop-Package'),
            'value'             => $meta->pie_data()->uses_image_upload() ? 'yes' : 'no',
            'desc_tip'          => true,
            'description'       => __('Require image uploads before you enter the editor. These images will be used to fill in placeholders, ie. a photobook', 'Peleman-Webshop-Package'),
            'wrapper_class'     => 'form-row form-row-first pwp-form-row-padding-5',
            'custom_attributes' => array('foldout' => $uploadToggle),
        ));

        HTML_Builder::open_div([
            'id' => $uploadToggle,
            'classes' => $meta->pie_data()->uses_image_upload() ? [] : ['pwp-hidden']
        ]);

        woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_PIE_Data::NUM_PAGES_KEY),
            'name'              => $this->format_loop_id(Product_PIE_Data::NUM_PAGES_KEY),
            'label'             => __('Pages to Fill', 'Peleman-Webshop-Package'),
            'value'             => $meta->pie_data()->get_num_pages(),
            'desc_tip'          => true,
            'description'       =>  __('Number of pages to fill in, this will be used for templates that have multiple pages, ie. a photobook', 'Peleman-Webshop-Package'),
            'wrapper_class'     => 'form-row form-row-last pwp-form-row-padding-5',
            'type'              => 'number',
            'custom_attributes' => array(
                'step'              => 1,
                'min'               => 0
            ),
            'placeholder'       => 0
        ));

        woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_PIE_Data::MIN_IMAGES_KEY),
            'name'              => $this->format_loop_id(Product_PIE_Data::MIN_IMAGES_KEY),
            'label'             => __('Min Images for upload', 'Peleman-Webshop-Package'),
            'value'             => $meta->pie_data()->get_min_images(),
            'desc_tip'          => true,
            'description'       =>  __('Minimum images that users are required to upload', 'Peleman-Webshop-Package'),
            'wrapper_class'     => 'form-row form-row-first pwp-form-row-padding-5',
            'type'              => 'number',
            'custom_attributes' => array(
                'step'              => 1,
                'min'               => 0
            ),
            'placeholder'       => 0
        ));

        woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_PIE_Data::MAX_IMAGES_KEY),
            'name'              => $this->format_loop_id(Product_PIE_Data::MAX_IMAGES_KEY),
            'label'             => __('Max images for upload', 'Peleman-Webshop-Package'),
            'value'             => $meta->pie_data()->get_max_images(),
            'desc_tip'          => true,
            'description'       =>  __('Maximum images that users are required to upload', 'Peleman-Webshop-Package'),
            'wrapper_class'     => 'form-row form-row-last pwp-form-row-padding-5',
            'type'              => 'number',
            'custom_attributes' => array(
                'step'              => 1,
                'min'               => 0
            ),
            'placeholder'       => 0
        ));

        woocommerce_wp_checkbox(array(
            'id'            => $this->format_loop_id(Product_PIE_Data::AUTOFILL_KEY),
            'name'          => $this->format_loop_id(Product_PIE_Data::AUTOFILL_KEY),
            'label'         => __('Autofill template pages in editor', 'Peleman-Webshop-Package'),
            'value'         => $meta->pie_data()->get_autofill() ? 'yes' : 'no',
            'desc_tip'      => true,
            'description'   => __('Autofill the template pages inside the editor', 'Peleman-Webshop-Package'),
            'wrapper_class' => 'form-row form-row-first pwp-form-row-padding-5',
        ));

        HTML_Builder::close_div();

        woocommerce_wp_checkbox(array(
            'id'            => $this->format_loop_id(Product_Meta_Data::OVERRIDE_CART_THUMB),
            'name'          => $this->format_loop_id(Product_Meta_Data::OVERRIDE_CART_THUMB),
            'label'         => __('Use project preview thumbnail in cart', 'Peleman-Webshop-Package'),
            'value'         => $meta->get_override_thumbnail() ? 'yes' : 'no',
            'desc_tip'      => true,
            'description'   => __('Show a preview of the project when the product is added to the cart', 'Peleman-Webshop-Package'),
            'wrapper_class' => 'form-row form-row-first pwp-form-row-padding-5',
        ));

        $this->render_editor_instructions($meta);
        HTML_Builder::close_div();
    }

    private function render_editor_instructions(Product_Meta_Data $meta): void
    {
    ?>
        <div class="pwp-options-header">Editor Instructions</div>
<?php
        HTML_Builder::open_div();
        $instructions = $meta->pie_data()->get_editor_instructions();
        $index = 0;
        foreach ($instructions as $key => $instruction) {
            woocommerce_wp_checkbox(array(
                'id'            => $this->format_loop_id($key),
                'name'          => $this->format_loop_id($key),
                'label'         => $instruction->get_label(),
                'value'         => $instruction->is_enabled() ? 'yes' : 'no',
                'desc_tip'      => true,
                'description'   => $instruction->get_description(),
                'wrapper_class' => 'form-row-multi-3 pwp-form-row-padding-5',
            ));
            $index++;
        }
        HTML_Builder::close_div();
    }

    private function render_PDF_upload_settings(Product_Meta_Data $meta): void
    {
        $editorToggle = 'pdf_required_' . $this->loop;
        $required = 'pdf_req_' . $this->loop;

        woocommerce_wp_checkbox(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::USE_PDF_CONTENT_KEY),
            'name'              =>  $this->format_loop_id(Product_Meta_Data::USE_PDF_CONTENT_KEY),
            'label'             => __('Require PDF upload', 'Peleman-Webshop-Package'),
            'value'             => $meta->uses_pdf_content() ? 'yes' : 'no',
            'desc_tip'          => true,
            'description'       => __('Enable/disable PDF upload for this product/variation', 'Peleman-Webshop-Package'),
            'wrapper_class'     => 'form-row form-row-first pwp-form-row-padding-5',
            'custom_attributes' => array('foldout' => $editorToggle, 'requires' => $required)
        ));

        HTML_Builder::open_div(array(
            'id'                => $editorToggle,
            'classes'           => $meta->uses_pdf_content() ? [] : ['pwp-hidden']
        ));

        woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::PDF_PRICE_PER_PAGE_KEY),
            'name'              => $this->format_loop_id(Product_Meta_Data::PDF_PRICE_PER_PAGE_KEY),
            'label'             => __('PDF price per page', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_price_per_page(),
            'desc_tip'          => true,
            'description'       => __('Additional price per page that will be added to product/variation price', 'Peleman-Webshop-Package'),
            'class'             => "{$required}",
            'wrapper_class'     => 'form-row form-row-last pwp-form-row-padding-5',
            'data_type'         => 'price',
            // 'custom_attributes' => array(
            //     'step' =>  wc_format_localized_decimal(0.001),
            //     'min' => wc_format_localized_decimal(0.000),
            // ),
            'placeholder' => wc_format_localized_decimal(0.000),
        ));

        woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::PDF_MIN_PAGES_KEY),
            'name'              => $this->format_loop_id(Product_Meta_Data::PDF_MIN_PAGES_KEY),
            'label'             => __('PDF Min Pages', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_pdf_min_pages() ?: 1,
            'desc_tip'          => true,
            'description'       =>  __('Minimum number of pages required for PDF upload', 'Peleman-Webshop-Package'),
            'class'             => $required,
            'wrapper_class'     => 'form-row form-row-first pwp-form-row-padding-5',
            'type'              => 'number',
            'custom_attributes' => array(
                'step'          => 1,
                'min'   => 1,
                'max'   => 1000
            ),
            'placeholder' => 1
        ));

        woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::PDF_MAX_PAGES_KEY),
            'name'              => $this->format_loop_id(Product_Meta_Data::PDF_MAX_PAGES_KEY),
            'label'             => __('PDF Max Pages', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_pdf_max_pages() ?: 1,
            'desc_tip'          => true,
            'description'       =>  __('Maximum number of pages allowed for PDF upload', 'Peleman-Webshop-Package'),
            'class'             => $required,
            'wrapper_class'     => 'form-row form-row-last pwp-form-row-padding-5',
            'type'              => 'number',
            'custom_attributes' => array(
                'step'              => 1,
                'min'               => 1,
                'max'               => 1000
            ),
            'placeholder'       => 1
        ));

        woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::PDF_WIDTH_KEY),
            'name'              => $this->format_loop_id(Product_Meta_Data::PDF_WIDTH_KEY),
            'label'             => __('PDF Format Width (mm)', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_pdf_width() ?: 1,
            'desc_tip'          => true,
            'description'       =>  __('permitted width of PDF uploads in mm', 'Peleman-Webshop-Package'),
            'class'             => $required,
            'wrapper_class'     => 'form-row form-row-first pwp-form-row-padding-5',
            'type'              => 'number',
            'custom_attributes' => array(
                'step'              => 1,
                'min'               => 1
            ),
            'placeholder'       => 210
        ));

        woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::PDF_HEIGHT_KEY),
            'name'              => $this->format_loop_id(Product_Meta_Data::PDF_HEIGHT_KEY),
            'label'             => __('PDF Format Height (mm)', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_pdf_height() ?: 1,
            'desc_tip'          => true,
            'description'       =>  __('permitted height of PDF uploads in mm', 'Peleman-Webshop-Package'),
            'class'             => $required,
            'wrapper_class'     => 'form-row form-row-last pwp-form-row-padding-5',
            'type'              => 'number',
            'custom_attributes' => array(
                'step'              => 1,
                'min'               => 1
            ),
            'placeholder' => 297
        ));

        woocommerce_wp_checkbox(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::PDF_SIZE_CHECK),
            'name'              => $this->format_loop_id(Product_Meta_Data::PDF_SIZE_CHECK),
            'label'             => __('PDF format check enabled', 'Peleman-Webshop-Package'),
            'value'             => $meta->pdf_size_check_enabled() ? 'yes' : 'no',
            'desc_tip'          => true,
            'description'       => __('Enable/disable PDF size validation.', 'Peleman-Webshop-Package'),
            'class'             => $required,
            'wrapper_class'     => 'form-row form-row-first pwp-form-row-padding-5',
            // 'custom_attributes' => array('foldout' => $custom2),
        ));
		
		woocommerce_wp_text_input(array(
            'id'                => $this->format_loop_id(Product_Meta_Data::PDF_MARGIN_ERROR_KEY),
            'name'              => $this->format_loop_id(Product_Meta_Data::PDF_MARGIN_ERROR_KEY),
            'label'             => __('PDF Margin of Error (mm)', 'Peleman-Webshop-Package'),
            'value'             => $meta->get_pdf_margin_error() ?: 1,
            'desc_tip'          => true,
            'description'       =>  __('Standard error of the dimensions of the uploaded pdf', 'Peleman-Webshop-Package'),
            'class'             => $required,
            'wrapper_class'     => 'form-row form-row-last pwp-form-row-padding-5',
            'type'              => 'number',
            'custom_attributes' => array(
                'step'              => 1,
                'min'               => 0
            ),
            'placeholder' => 5
        ));

        HTML_Builder::close_div();
    }

    private function format_loop_id(string $id): string
    {
        return $id . $this->loopSuffix;
    }
}