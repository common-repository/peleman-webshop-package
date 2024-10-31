<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;
use PWP\includes\services\entities\Project;
use PWP\includes\utilities\pdfHandling\PDFI_PDF_Factory;


class Add_Reference_Project_To_Cart_Item extends Abstract_Filter_Hookable
{
    public function __construct()
    {
        parent::__construct('pwp_add_cart_item_data', 'add_to_cart_item', 30, 3);
    }

    public function add_to_cart_item(array $data, \WC_Product $product, Product_Meta_Data $meta): array
    {
      
        $data['_project_reference'] = $meta->get_project_reference();
    

    return $data;
   
    }
}
