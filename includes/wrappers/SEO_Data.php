<?php

declare(strict_types=1);

namespace PWP\includes\wrappers;

class SEO_Data extends Component
{
    public function get_description(): string
    {
        return $this->data->description ?: '';
    }

    public function get_focus_keyword(): string
    {
        return $this->data->focus_keyword ?: '';
    }
}
