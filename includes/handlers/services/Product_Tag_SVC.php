<?php

declare(strict_types=1);

namespace PWP\includes\handlers\services;

use PWP\includes\handlers\services\Term_SVC;

class Product_Tag_SVC extends Term_SVC
{
    public function __construct(string $sourceLang = 'en')
    {
        parent::__construct(
            'product_tag',
            'tax_product_tag',
            'product tag',
            $sourceLang
        );
    }
}
