<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;

class Get_PIE_Product_Meta_Filter extends Abstract_Filter_Hookable
{
    public function __construct()
    {
        parent::__construct('pwp_get_product_meta', 'pwp_get_product_meta_data', 10, 3);
    }

    public function pwp_get_product_meta_data($data, int $productId, string $metaKey)
    {
        $product = wc_get_product($productId);
        if (empty($product) || empty($metaKey)) {
            return $data;
        }

        $meta = $product->get_meta($metaKey, true);
        return $meta;
    }
}
