<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use PWP\includes\utilities\notification\I_Notice;
use PWP\includes\utilities\response\Response;

class Create_Variation_Product_Command extends Create_Product_Command
{
    private \WC_Product $parent;
    public function __construct(\WC_Product_Variable $parent, array $data)
    {
        $this->parent = $parent;
        parent::__construct($data);
    }

    public function do_action(): I_Notice
    {
        return Response::failure('not implemented', 'method not yet implemented.', 501);
    }
}
