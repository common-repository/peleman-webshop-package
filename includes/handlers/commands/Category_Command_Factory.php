<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use PWP\includes\handlers\services\Term_SVC;
use PWP\includes\wrappers\Term_Data;

final class Category_Command_Factory extends Abstract_Term_Command_Factory
{
    public function __construct(string $defaultLang = 'en')
    {
        $service = new Term_SVC('product_cat', 'tax_product_cat', "product category", $defaultLang);
        parent::__construct($service);
    }

    final public function new_create_term_command(Term_Data $data): Create_Term_Command
    {
        return new Create_Term_Command($this->service, $data);
    }

    final public function new_read_term_command(array $args = []): Read_Term_Command
    {
        return new Read_Term_Command($this->service, $args);
    }

    final public function new_update_term_command(Term_Data $data, bool $canChangeParent = false): Update_Term_Command
    {
        return new Update_Term_Command($this->service, $data, $canChangeParent);
    }

    final public function new_delete_term_command(string $slug): Delete_Term_Command
    {
        return new Delete_Term_Command($this->service, $slug);
    }

    final public function new_create_or_update_command(Term_Data $data, bool $canChangeParent): I_Command
    {
        return $this->slug_exists($data->get_slug())
            ? $this->new_update_term_command($data, $canChangeParent)
            : $this->new_create_term_command($data);
    }

    final public function get_service(): Term_SVC
    {
        return $this->service;
    }
}
