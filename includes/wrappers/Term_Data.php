<?php

declare(strict_types=1);

namespace PWP\includes\wrappers;

use PWP\includes\wrappers\Component;
use PWP\includes\exceptions\Invalid_Input_Exception;

class Term_Data extends Component
{
    final protected function generate_slug(string $name, ?string $lang = null): string
    {
        $slug = str_replace(' ', '_', strtolower($name));

        if (!empty($lang)) {
            $slug .= "-{$lang}";
        }
        $this->data->slug = $slug;
        $this->data->parent = $this->data->parent_id;
        unset($this->data->parent_id);
        return $slug;
    }

    final public function get_seo_data(): ?SEO_Data
    {
        if (!isset($this->data->seo)) return null;
        return new SEO_Data($this->data->seo);
    }

    final public function get_translation_data(): Translation_Data
    {
        return new Translation_Data(
            array(
                'english_slug' => $this->data->english_slug,
                'language_code' => $this->data->language_code,
            )
        );
    }

    final public function has_translation_data(): bool
    {
        return ($this->data->english_slug && $this->data->language_code);
    }

    final public function get_name(): string
    {
        return $this->data->name;
    }

    final public function get_slug(): ?string
    {
        return $this->data->slug;
    }

    final public function get_new_slug(): ?string
    {
        return $this->data->new_slug;
    }

    final public function get_description(): string
    {
        return $this->data->description ?: '';
    }

    final public function get_parent_slug(): string
    {
        return $this->data->parent ?: '';
    }

    final public function get_parent_id(): int
    {
        return (int)($this->data->parent_id ?: '');
    }

    final public function set_parent(int $id): void
    {
        $this->data->parent_id = $id;
    }

    final public function set_parent_slug(string $slug): void
    {
        $this->data->parent = $slug;
    }
}
