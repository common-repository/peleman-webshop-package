<?php

declare(strict_types=1);

namespace PWP\includes\handlers;

use WC_Product;
use PWP\includes\exceptions\Not_Found_Exception;
use PWP\includes\exceptions\Not_Implemented_Exception;
use PWP\includes\handlers\services\Term_SVC;


class Product_Handler
{
    public function __construct()
    {
    }

    public function create_item(array $createData, array $args = []): object
    {
        $createData = (object)$createData;
        try {
            $product = new WC_Product();

            $product->set_name($createData->name);
            $product->set_reviews_allowed(false);

            if (!empty($createData->lang)) {
                $parentId = wc_get_product_id_by_sku($createData->SKU);
                if (empty($parentId)) {
                    throw new \Exception("Parent product not found (default language counterpart not found in database)", 400);
                }
            }
            if (wc_get_product_id_by_sku($createData->SKU) > 0) {
                throw new \Exception("product with this SKU already exists!", 400);
            }

            $product->set_SKU($createData->SKU);
            $product->set_status($createData->status);

            $product->set_catalog_visibility($createData->visibility ?: 'hidden');

            if (!empty($createData->parent_id)) {
                $product->set_parent_id($createData->parent);
            }
            $product->set_price($createData->price);
            $product->set_regular_price($createData->regular_price);
            $product->set_sale_price($createData->sale_price);

            $product->set_upsell_ids($this->get_ids_from_skus($createData->upsell_SKUs));
            $product->set_cross_sell_ids($this->get_ids_from_skus($createData->cross_sell_SKUs));

            $product->set_tag_ids($this->get_tag_ids($createData->tags));
            $product->set_category_ids($this->get_category_ids($createData->categories));
            $product->set_attributes($this->get_attributes($createData->attributes));
            $product->set_default_attributes($this->get_default_attributes($createData->default_attributes));

            $product->set_image_id($createData->main_image_id);
            $product->set_gallery_image_ids($this->get_images($createData->images));

            $product->set_meta_data(array('customizable' => $createData->customizable ?: false));
            $product->set_meta_data(array('template-id' => $createData->template_id));
            $product->set_meta_data(array('template-variant-id' => $createData->template_variant_id));
            if (!empty($createData->lang)) {
                $product->set_meta_data(array('lang' => $createData->lang));
            }
        } catch (\Exception $exception) {
            throw $exception;
        }

        $id = $product->save();

        if ($id <= 0) {
            throw new \Exception("something went wrong when trying to save a new product!", 500);
        }

        return $product;
    }

    public function get_item(int $id, array $args = []): object
    {
        $response = wc_get_product($id);
        if (!$response || is_null($response)) {
            throw new \Exception("no product matching id {$id} found in database!", 404);
        }

        return $response;
    }

    public function get_items(array $args = []): array
    {
        $args['paginate'] = 'true';
        $args['return'] = 'objects';

        $results = (array)wc_get_products($args);
        $results['products'] = $this->remap_results_array($results['products']);

        return $results;
    }

    public function update_item(int $id, array $createData, array $args = [], bool $useNullValues = false): object
    {
        throw new Not_Implemented_Exception(__METHOD__);
    }

    public function delete_item(int $id, array $args = []): bool
    {
        $forceDelete = $args['force'] ? (bool)$args['force'] : false;

        $product = $this->get_item($id, $args);
        if (!$product instanceof WC_Product) throw new \Exception("value retrieved by database not of proper type!", 404);

        $childIds = $product->get_children();
        foreach ($childIds as $childId) {
            $this->get_item($childId)->delete($forceDelete);
        }
        return $product->delete($forceDelete);
    }

    private function get_ids_from_skus(?array $skus): array
    {
        if (is_null($skus)) return array();
        $ids = array();
        foreach ($skus as $sku) {
            $id = wc_get_product_id_by_sku($sku);
            if ($id <= 0) throw new \Exception("invalid product SKU entered: {$sku}");

            $ids[] = $id;
        }

        return $ids;
    }

    private function get_images(?array $ids): array
    {
        throw new Not_Implemented_Exception(__METHOD__);
    }

    private function get_tag_ids(?array $slugs): array
    {
        if (is_null($slugs)) return array();

        $tagIds = array();
        $handler = new Term_SVC('product_cat', 'tax_product_cat', "product category");
        foreach ($slugs as $slug) {
            $result =  $handler->get_item_by_slug($slug);
            if (is_null($result)) {
                throw new Not_Found_Exception("tag with slug {$slug} not found in system");
            }
            $tagIds[] = $result->term_id;
        }

        return $tagIds;
    }

    private function get_category_ids(?array $slugs): array
    {
        throw new Not_Implemented_Exception(__METHOD__);
    }

    private function get_attributes(?array $slugs): array
    {
        if (is_null($slugs)) return [];

        $handler = new Product_Attribute_Handler();
        $attributeIds = array();
        foreach ($slugs as $slug) {
            $attribute = $handler->get_attribute_by_slug($slug);
            if (is_null($attribute)) {
                continue;
            }
            $attributeIds[] = $attribute['id'];
        }

        return $attributeIds;
    }

    private function get_default_attributes(?array $slugs): array
    {
        throw new Not_Implemented_Exception(__METHOD__);
    }

    private function remap_results_array(array $products): array
    {
        return array_map(
            function ($product) {
                if (!$product instanceof \WC_Product) {
                    return $product;
                }
                $data = $product->get_data();
                $data['variations'] = $product->get_children();
                return $data;
            },
            $products
        );
    }
}
