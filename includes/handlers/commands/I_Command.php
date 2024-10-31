<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use PWP\includes\utilities\notification\I_Notice;

interface I_Command
{
    public function do_action(): I_Notice;
}
