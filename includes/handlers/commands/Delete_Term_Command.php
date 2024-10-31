<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use WP_Term;
use PWP\includes\wrappers\Term_Data;
use PWP\includes\handlers\services\Term_SVC;
use PWP\includes\utilities\notification\Error_Notice;
use PWP\includes\validation\Validation_Handler;
use PWP\includes\validation\Abstract_Term_Handler;
use PWP\includes\utilities\notification\Notification;
use PWP\includes\validation\Validate_Term_Slug_Exists;
use PWP\includes\utilities\notification\Success_Notice;
use PWP\includes\utilities\notification\I_Notice;

final class Delete_Term_Command implements I_Command
{
    private Term_SVC $service;
    private string $slug;

    private Abstract_Term_Handler $handler;

    public function __construct(Term_SVC $service, string $slug)
    {
        $this->service = $service;
        $this->slug = $slug;

        $this->handler = new Validation_Handler($this->service);
        $this->handler->set_next(new Validate_Term_Slug_Exists($this->service));
    }

    public function do_action(): I_Notice
    {
        $notification = new Notification();
        $data = new Term_Data(['slug' => $this->slug]);
        $response = $this->handler->handle($data, $notification);

        if (!$response) return $notification;
        if ($this->delete_term()) {
            return $notification->add_error(
                "category could not be deleted",
                "deletion of {$this->service->get_taxonomy_name()} {$this->slug} failed for unknown reasons."
            );
        }

        return new Success_Notice(
            "term deleted",
            "term {$this->service->get_taxonomy_name()} with slug {$this->slug} has been successfully deleted!"
        );
    }


    public function undo_action(): I_Notice
    {
        return new Error_Notice(
            "method not implemented",
            "method " . __METHOD__ . " not implemented"
        );
    }

    private function delete_term(): bool
    {
        $term = $this->service->get_item_by_slug($this->slug);

        $this->service->unparent_children($term);

        return $this->service->delete_item($term);
    }
}
