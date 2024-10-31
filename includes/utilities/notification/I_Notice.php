<?php

declare(strict_types=1);

namespace PWP\includes\utilities\notification;

interface I_Notice
{
    public function get_message(): string;
    public function get_description(): string;
    public function to_array(): array;
    public function is_success(): bool;
}
