<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;


use PWP\includes\utilities\notification\I_Notice;
use PWP\includes\utilities\response\Response;
use WC_Product_Variation;

final class Create_Product_Variation_Command implements I_Command
{
    public function __construct()
    {
    }
    public function do_action(): I_Notice
    {
        $variation = new WC_Product_Variation();
        // $variation->save();
        return Response::failure(
            "method not implemented",
            "method " . __METHOD__ . " not implemented. Undo actions on database entries are not doable."
        );
    }

    public function undo_action(): I_Notice
    {
        return Response::failure(
            "method not implemented",
            "method " . __METHOD__ . " not implemented. Undo actions on database entries are not doable."
        );
    }
}
