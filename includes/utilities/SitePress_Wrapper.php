<?php

declare(strict_types=1);

namespace PWP\includes\utilities;

use SitePress;

class SitePress_Wrapper

{
    /**@phpstan-ignore-next-line */
    public ?SitePress $sitepress;
    private bool $sitepressOverrideActive;

    public function __construct()
    {
        if (class_exists('SitePress')) {
            global $sitepress;
            $this->sitepress = $sitepress;
        } else {
            $this->sitepress = null;
        }
        $this->sitepressOverrideActive = false;
    }

    /**@phpstan-ignore-next-line */
    final public function get_sitepress(): ?SitePress
    {
        return $this->sitepress;
    }

    /**
     * disable sitepress filter that adjusts taxonomy ids automatically when calling get_term
     * more information at : https://stackoverflow.com/questions/70789572/wp-term-query-with-wpml-translated-custom-taxonomy
     * @return void
     */
    final public function disable_sitepress_get_term_filter(): void
    {
        if ($this->sitepressOverrideActive) {
            return;
        }

        remove_filter("get_term", array($this->sitepress, 'get_term_adjust_id'), 1);
        remove_filter("get_terms_args", array($this->sitepress, "get_terms_args_filter"), 10);
        remove_filter("terms_clauses", array($this->sitepress, "terms_clauses"), 10);

        $this->sitepressOverrideActive = true;
    }

    /**
     * enable sitepress filter that adjusts taxonomy ids automatically when calling get_term
     * more information at : https://stackoverflow.com/questions/70789572/wp-term-query-with-wpml-translated-custom-taxonomy
     * @return void
     */
    final public function enable_sitepress_get_term_filter(): void
    {
        if (!$this->sitepressOverrideActive) {
            return;
        }

        add_filter("get_term", array($this->sitepress, 'get_term_adjust_id'), 1, 1);
        add_filter("get_terms_args", array($this->sitepress, "get_terms_args_filter"), 10, 2);
        add_filter("terms_clauses", array($this->sitepress, "terms_clauses"), 10, 3);

        $this->sitepressOverrideActive = false;
    }

    final public function get_active_languages(bool $refresh = false, bool $majorFirst = false): array
    {
        if (!$this->sitepress) {
            return get_available_languages();
        }

        /** @phpstan-ignore-next-line */
        $langs = $this->sitepress->get_active_languages($refresh, $majorFirst);
        return array_keys($langs);
    }

    /**
     * @param integer $element_id
     * @param string $el_type
     * @return bool|mixed|null|string
     */
    final public function get_element_trid(int $element_id, string $el_type)
    {
        if (!$this->sitepress) {
            return null;
        }

        /** @phpstan-ignore-next-line */
        return $this->sitepress->get_element_trid($element_id, $el_type);
    }

    /**
     * Undocumented function
     *
     * @param integer $element_id
     * @param string $element_type
     * @param boolean $return_original_if_missing
     * @param string|null $language_code
     * @return integer|null
     */
    final public function get_object_id(int $element_id, string $element_type, bool $return_original_if_missing = false, string $language_code = null): ?int
    {
        if (!$this->sitepress) {
            return null;
        }

        /** @phpstan-ignore-next-line */
        return $this->sitepress->get_object_id($element_id, $element_type, $return_original_if_missing, $language_code);
    }
}
