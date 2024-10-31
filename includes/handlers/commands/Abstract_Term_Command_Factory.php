<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use PWP\includes\handlers\services\Term_SVC;
use PWP\includes\wrappers\Term_Data;

/**
 * Abstract base class for Term Command factories.
 */
abstract class Abstract_Term_Command_Factory
{
    protected Term_SVC $service;

    public function __construct(Term_SVC $service)
    {
        $this->service = $service;
        $this->service->disable_sitepress_get_term_filter();
    }

    abstract public function new_create_term_command(Term_Data $data): Create_Term_Command;

    abstract public function new_read_term_command(array $args = []): Read_Term_Command;

    abstract public function new_update_term_command(Term_Data $data, bool $canChangeParent): Update_Term_Command;

    abstract public function new_delete_term_command(string $slug): Delete_Term_Command;

    final public function slug_exists(string $slug): bool
    {
        return !is_null($this->service->get_item_by_slug($slug));
    }
}
