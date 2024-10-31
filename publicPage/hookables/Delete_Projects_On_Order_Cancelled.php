<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\services\entities\Project;

/**
 * Deletes .pdf projects when an order is cancelled
 */
class Delete_Projects_On_Order_Cancelled extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_order_status_cancelled', 'delete_projects');
    }

    public function delete_projects(int $order_id): void
    {
        $order = wc_get_order($order_id);
        if (!$order) return;

        $items = $order->get_items();

        foreach ($items as $i => $item) {
            if (!$item->get_meta('_pdf_data')) return;
            $pdf_data = $item->get_meta('_pdf_data');
            $id = (int)$pdf_data['id'];

            $project = Project::get_by_id($id);
            $project->delete_files();
            $project->delete();
        }
    }
}
