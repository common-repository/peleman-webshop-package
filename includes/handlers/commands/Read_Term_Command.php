<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use PWP\includes\handlers\services\Term_SVC;
use PWP\includes\utilities\notification\Error_Notice;
use PWP\includes\validation\Validation_Handler;
use PWP\includes\utilities\notification\Success_Notice;
use PWP\includes\validation\Abstract_Term_Handler;
use PWP\includes\utilities\notification\Notification;
use PWP\includes\utilities\notification\I_Notification;
use PWP\includes\utilities\notification\I_Notice;
use PWP\includes\utilities\response\I_Response;

final class Read_Term_Command implements I_Command
{
    protected Abstract_Term_Handler $handler;
    protected Term_SVC $service;
    protected array $args;

    public function __construct(Term_SVC $service, array $args = [])
    {
        $this->service = $service;
        $this->args = $args;

        $this->handler = new Validation_Handler($this->service);
    }

    public function do_action(): I_Notice
    {
        $this->service->enable_sitepress_get_term_filter();
        $items = $this->service->get_items($this->args);
        $itemArray = $this->get_data_from_items($items);
        $this->service->disable_sitepress_get_term_filter();

        // return Response::success("Terms", array("results" => count($itemArray), 'items' => $itemArray));
        return new Success_Notice("Terms", $this->service->get_taxonomy_name(), array(
            'results' => count($itemArray),
            'items' => $itemArray
        ));
    }

    public function undo_action(): I_Notice
    {
        return new Error_Notice("method not implemented", "method " . __METHOD__ . " not implemented");
    }

    private function get_data_from_items(array $items): array
    {
        return array_map(function ($e) {
            return (array)$e->data;
        }, $items);
    }
}
