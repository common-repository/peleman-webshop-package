<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use PWP\includes\utilities\notification\I_Notice;
use PWP\includes\utilities\response\Error_Response;
use PWP\includes\utilities\response\Response;
use WC_Product_Simple;

class Create_Simple_Product_Command extends Create_Product_Command
{
    public function do_action(): I_Notice
    {
        if (isset($this->data['lang']) && 'en' !== $this->data['lang']) {
            $lang = $this->data['lang'];
            $sku = $this->data['sku'];
            $parent = wc_get_product(wc_get_product_id_by_sku($this->data['sku']));
            //unset SKU to prevent duplicate SKU errors.
            unset($this->data['sku']);
            if (empty($parent) || !$parent) {
                return new Error_Response(
                    'original translation not found.',
                    400,
                );
            }
        }

        //check for duplicate SKU
        if (wc_get_product_id_by_sku($this->data['sku'])) {
            return new Error_Response(
                'product with this SKU already exists',
                400,
                array('sku' => $this->data['sku'])
            );
        }
        $product = new WC_Product_Simple();
        $this->set_product_params_from_data($product);
        $productId = $product->save();

        if (0 >= $productId)
            return new Error_Response("Something went wrong trying to save a new product, 404");

        $this->handle_product_meta_components($product);

        // $this->configure_translation($product, $parent);
        return Response::success(
            "success",
            "Product successfully created",
            200,
            $product->get_data()
        );
    }
}
