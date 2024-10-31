<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Ajax_Hookable;
use WC_Product;

/**
 * Ajax hookables which updates the woocommerce product page of a variable product
 * whenever a customer chooses a different variation.
 */
class Ajax_Show_Variation extends Abstract_Ajax_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct(
            'Ajax_Show_Variation',
            plugins_url('../js/pwp-show-variation.js', __FILE__),
            $priority,
            [
                'pdfjs',
                'pdfworkerjs'
            ],
        );
    }

    public function callback(): void
    {
        $variantId = sanitize_key($_REQUEST['variant']);
        $variant = wc_get_product($variantId);
        $meta = new Product_Meta_Data($variant);
        $parent = wc_get_product($variant->get_parent_id());

        $response = array(
            'variant'                   => $variantId,
            'in_stock'                  => $variant->is_in_stock(),
            'is_customizable'           => $meta->is_customizable(),
            'requires_pdf_upload'       => $meta->uses_pdf_content(),
            'use_reference_project'     =>$meta->get_use_project_reference(),
            'button_text'               => $this->get_add_to_cart_label($meta, $parent),
            'is_bundle'                 => $meta->get_unit_amount() > 1,
            'unit_amount'               => " (" . $meta->get_unit_amount() . ' ' . __('pieces', 'Peleman-Webshop-Package') . ")",
            'unit_price'                => wc_price($meta->get_unit_price()),
            'item_price'                => wc_price(floatval($variant->get_price())),
            'f2dArtCode'                => $meta->get_f2d_article_code(),
            'MinQuantity'               => $meta->get_min_quantity(),
            'IncrementStep'             => $meta->get_increment_step(),
            'pdf_data'                  => array(
                'width'                 => $meta->get_pdf_width() ? $meta->get_pdf_width() . ' mm' : '',
                'height'                => $meta->get_pdf_height() ? $meta->get_pdf_height() . ' mm' : '',
                'min_pages'             => $meta->get_pdf_min_pages() ? $meta->get_pdf_min_pages() : '',
                'max_pages'             => $meta->get_pdf_max_pages() ? $meta->get_pdf_max_pages() : '',
                'price_per_page'        => $meta->get_price_per_page() ?: 0,
                'price_per_page_html'   => $meta->get_price_per_page() ? wc_price($meta->get_price_per_page()) : '',
                'total_price'           => $variant->get_price(),
            ),
        );

        $data = apply_filters('pwp_get_variation_custom_fields', [], $variant);
        /**
         * 
         * example element to enter into the associative array.
         * $element = array(
         *  'target_id'      => 'my_id',
         *  'target_class'   => 'my_class',
         *  'hide_element'   => false,
         *  'inner_html'     => 'my message'
         *);
         */

        $response['extra_elements'] = $data;
        wp_send_json_success($response, 200);
    }

    public function callback_nopriv(): void
    {
        $this->callback();
    }

    private function get_add_to_cart_label(Product_Meta_Data $meta, WC_Product $parent): string
    {
        if (!empty($meta->get_custom_add_to_cart_label()))
            return $meta->get_custom_add_to_cart_label();
        if (!empty($parent->get_meta('custom_add_to_cart_label')))
            return $parent->get_meta('custom_add_to_cart_label');
        if ($meta->is_customizable()) {
            return get_option('pwp_customize_label', null) ?: __('Customize Product', 'Peleman-Webshop-Package');
        }
        return __('Add To Cart', 'woocommerce');
    }

    protected function object_data(): array
    {
        return array('loading_msg' => __("Loading", 'Peleman-Webshop-Package'));
    }
}