<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use PWP\includes\F2D\I_Meta_Property;

abstract class Product_Meta implements I_Meta_Property
{
    protected \WC_Product $parent;
    protected array $data;

    public function __construct(\WC_Product $parent)
    {
        $this->parent = $parent;
        $this->data = $parent->get_meta_data();
    }

    final public function get_parent(): \WC_Product
    {
        return $this->parent;
    }

    abstract function update_meta_data(): void;

    public function save_meta_data(): void
    {
        $this->parent->save();
        $this->parent->save_meta_data();
    }
}
