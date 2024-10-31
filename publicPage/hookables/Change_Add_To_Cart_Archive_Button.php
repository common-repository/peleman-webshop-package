<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;

/**
 * change archive button text and button for products which are either customizable or require PDF uploads before
 * they can be added to cart.
 */
class Change_Add_To_Cart_Archive_Button extends Abstract_Filter_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_loop_add_to_cart_link', 'redirect_add_to_cart', 16, 2);
    }

    public function redirect_add_to_cart(string $button, \WC_Product $product): string
    {
        $meta = new Product_Meta_Data($product);

        if ($meta->uses_pdf_content() || $meta->is_editable()) {
            $label = $this->get_label($product, $meta);
            $label = __($label, 'Peleman-Webshop-Package');
            $redir = $product->get_permalink();
            $button = "<a class='button' href='{$redir}'>{$label}</a>";
        }

        return $button;
    }

    private function get_label(\WC_Product $product, Product_Meta_Data $meta): string
    {
        switch ($product->get_type()) {
            case 'simple':
                return  get_option('pwp_customize_label', __('add to cart', 'Peleman-Webshop-Package'));
            case 'variant':
            case 'variable':
                return get_option('pwp_archive_var_label', __('read more', 'Peleman-Webshop-Package'));
            case 'external':
            case 'grouped':
            case 'default':
            default:
                return __('add to cart', 'Peleman-Webshop-Package');
        }
    }
}
