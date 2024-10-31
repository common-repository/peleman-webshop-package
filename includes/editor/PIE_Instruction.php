<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use WC_Product;

final class PIE_Instruction
{
    private string $key;
    private string $label;
    private bool $enabled;
    private string $description;

    public function __construct(string $key, string $label, bool $enabled, string $description = '')
    {
        $this->key = $key;
        $this->label = $label;
        $this->description = $description;
        $this->enabled = $enabled;
    }

    public function get_key(): string
    {
        return $this->key;
    }
    
    public function set_enabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function get_description(): string
    {
        return $this->description;
    }

    public function is_enabled(): bool
    {
        return $this->enabled;
    }

    public function get_label(): string
    {
        return $this->label;
    }
}
