<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;
use PWP\includes\services\entities\Project;
use PWP\includes\utilities\pdfHandling\PDFI_PDF_Factory;

/**
 * Filter hookable class for handling PDF uploads when adding an item to the cart. If the product
 * - requires a pdf file and  
 * -  a pdf file is uploaded
 * - this class will generate a new record of the PDF in the database and add a reference
 * to the specific item in the cart.
 */
class Add_PDF_Data_To_Cart_Item extends Abstract_Filter_Hookable
{
    public function __construct()
    {
        parent::__construct('pwp_add_cart_item_data', 'add_PDF_to_cart_item', 30, 3);
    }

    public function add_PDF_to_cart_item(array $data, \WC_Product $product, Product_Meta_Data $meta): array
    {
        if (!$meta->uses_pdf_content() || !isset($_FILES['pdf-upload']))
            return $data;

        $fileArr = $_FILES['pdf-upload'];

        try {

            // Undefined | Multiple Files | $_FILES Corruption Attack
            // If this request falls under any of them, treat it invalid.

            if ('application/pdf' === $fileArr['type'] && 0 === $fileArr['error']) {

                if (4 === $fileArr['error']) {
                    wp_die('something went wrong with the file upload', 'upload failure');
                }

                $pdf = PDFI_PDF_Factory::generate_from_upload($fileArr);

                $filename = $pdf->get_name();
                $project = Project::create_new(
                    get_current_user_id(),
                    $product->get_id(),
                    $filename,
                    $pdf->get_page_count(),
                    $pdf->get_page_count() * $meta->get_price_per_page()
                );
                $project->save_file($pdf);

                $data['_pdf_data'] = array(
                    'id'            => $project->get_id(),
                    'pdf_name'      => $project->get_file_name(),
                    'pages'         => $pdf->get_page_count(),
                    'extra_cost'    => $pdf->get_page_count() * $meta->get_price_per_page()
                );
            }

            return $data;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
