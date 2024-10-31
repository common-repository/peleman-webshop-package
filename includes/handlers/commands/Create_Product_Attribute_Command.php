<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use PWP\includes\utilities\notification\I_Notice;
use PWP\includes\utilities\response\I_Response;
use PWP\includes\utilities\response\Response;

class Create_Product_Attribute_Command implements I_Command
{
    private string $name;
    private string $slug;
    private string $type;
    private string $orderBy;
    private bool $hasArchives;

    private string $taxonomy;

    public function __construct(string $name, string $slug, string $type, string $orderBy, bool $hasArchives)
    {
        $this->name = $name;
        $this->slug = stripslashes($slug);
        $this->type = $type;
        $this->orderBy = $orderBy;
        $this->hasArchives = $hasArchives;

        $this->taxonomy = 'pa_' . $this->slug;
    }

    public function do_action(): I_Notice
    {
        if (taxonomy_exists($this->taxonomy)) {
            return Response::failure('failure', 'Attribute already exists.', 409);
        }

        $id = wc_create_attribute(array(
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'order_by' => $this->orderBy,
            'has_archives' => $this->hasArchives,
        ));

        if ($id instanceof \WP_Error) {
            return Response::failure(
                'failure',
                'Attribute creation failed.',
                400
            );
        };

        $attr = wc_get_attribute($id);

        return Response::success(
            'success',
            'Attribute successfully created',
            200,
            (array)$attr
        );
    }

    public function get_taxonomy(): string
    {
        return $this->taxonomy;
    }
}
