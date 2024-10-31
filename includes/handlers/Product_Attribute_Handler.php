<?php

declare(strict_types=1);

namespace PWP\includes\handlers;

use PWP\includes\exceptions\Not_Implemented_Exception;

/**
 * Undocumented class
 * 
 * @deprecated version
 */
class Product_Attribute_Handler
{

    private array $taxonomyIds;

    public function get_item(int $id, array $args = []): object
    {
        return wc_get_attribute($id);
    }

    public function get_items(array $args = []): array
    {
        if (empty($this->taxonomyIds)) {
            //retrieve and cache woocommerce attribute taxonomy taxonomyIds
            $this->taxonomyIds = wc_get_attribute_taxonomy_ids();
        }
        $attributes = array();
        foreach ($this->taxonomyIds as $id) {
            $attributes[] = wc_get_attribute($id);
        }

        return $attributes;
    }

    public function create_item(string $name, array $attributes): object
    {
        throw new Not_Implemented_Exception(__METHOD__);
    }

    public function update_item(int $id, array $updateData, array $args = [], bool $useNullValues = false): object
    {
        throw new Not_Implemented_Exception(__METHOD__);
    }

    public function delete_item(int $id, array $args = []): bool
    {
        return false;
    }

    public function get_attribute_by_slug(string $slug): object
    {
        $attributes = $this->get_items();
        $attributes = array_filter($attributes, function ($e) use ($slug) {
            return $e['slug'] === $slug;
        });

        return $attributes[0];
    }
}
