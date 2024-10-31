<?php

declare(strict_types=1);

namespace PWP\includes\utilities\response;

use PWP\includes\utilities\notification\I_Notice;

interface I_Response
{
    public function to_array(): array;
    public function get_message(): string;
    public function get_code(): int;
}
