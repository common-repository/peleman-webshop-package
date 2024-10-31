<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use PWP\includes\utilities\notification\I_Notice;
use PWP\includes\utilities\response\Error_Response;
use PWP\includes\utilities\response\I_Response;
use PWP\includes\utilities\response\Response;
use WC_Product_Variable;

class Create_Variable_Product_Command extends Create_Product_Command
{
    public function do_action(): I_Notice
    {
        //check for duplicate SKU
        if (wc_get_product_id_by_sku($this->data['sku'])) {
            return new Error_Response(
                'product with this SKU already exists',
                400,
                array('sku' => $this->data['sku'])
            );
        }
        $product = new WC_Product_Variable();
        $this->set_product_params_from_data($product);
        $product->set_attributes($this->wc_prepare_product_attributes($this->data['attributes']));
        $productId = $product->save();

        if (0 >= $productId) {
            return new Error_Response("Something went wrong trying to save a new product, 404");
        }

        $this->handle_product_meta_components($product);

        return Response::success(
            'success',
            'new Variable product created',
            200,
            $product->get_data()
        );
    }

    public function wc_prepare_product_attributes(array $attributes): array
    {

        $data = array();
        $position = 0;

        foreach ($attributes as $values) {
            // $name => $values)
            $name = $values['name'];
            $taxonomy = 'pa_' . $name;
            if (!taxonomy_exists($taxonomy)) {
                error_log("taxonomy already exists: {$taxonomy}");
                continue;
            }

            // Get an instance of the WC_Product_Attribute Object
            $attribute = new \WC_Product_Attribute();

            $term_ids = array();
            $terms = $values['terms'];

            // Loop through the term names
            foreach ($values['terms'] as $term_name) {
                if (term_exists($term_name, $taxonomy))
                    // Get and set the term ID in the array from the term name
                    $term_ids[] = get_term_by('name', $term_name, $taxonomy)->term_id;
                else
                    continue;
            }

            $taxonomy_id = wc_attribute_taxonomy_id_by_name($taxonomy); // Get taxonomy ID
            // error_log('taxonomy id: ' . $taxonomy_id);

            $attribute->set_id($taxonomy_id);
            $attribute->set_name($name);
            // $attribute->set_options($term_ids);
            $attribute->set_options($terms);
            $attribute->set_position($position);
            $attribute->set_visible($values['is_visible'] ?: false);
            $attribute->set_variation($values['for_variation'] ?: false);

            $data[$taxonomy] = $attribute; // Set in an array

            $position++; // Increase position
        }
        return $data;
    }
}
