<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\services\entities\Project;

/**
 * When an item with a PDF upload is deleted from the cart,
 * this hook will remove the PDf from the projects table and the upload directory
 */
class Remove_PDF_On_Cart_Deletion extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_remove_cart_item', 'remove_pdf_project', 10, 2);
    }

    public function remove_pdf_project(string $cart_item_id, \WC_Cart $cart): void
    {
        $item = $cart->get_cart_item($cart_item_id);
        if (!isset($item['_pdf_data'])) return;

        $data = $item['_pdf_data'];
        $id = (int)$data['id'];

        if (0 >= $id) return;

        error_log("removing project from pwp uploads: {$id}");
        $project = Project::get_by_id($id);
        $project ? $project->delete() : null;
    }
}
