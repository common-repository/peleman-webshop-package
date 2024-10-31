<?php

declare(strict_types=1);

namespace PWP\includes\handlers\services;

use PWP\includes\utilities\PWP_WPDB;
use PWP\includes\wrappers\SEO_Data;
use PWP\includes\exceptions\WP_Error_Exception;
use PWP\includes\exceptions\Invalid_Input_Exception;
use PWP\includes\utilities\SitePress_Wrapper;
use WP_Term;

class Term_SVC
{
    private string $taxonomy;
    private string $taxonomyType;
    private string $taxonomyName;

    private string $sourceLang;

    private SitePress_Wrapper $sitepressHandler;

    /**
     * @param string $taxonomy taxonomy of the term
     * @param string $taxonomyType name of the element for use with WPML translations.
     * @param string $taxonomyName beautified name for use in human readable errors.
     * @param string $sourceLang 2 letter lower-case language code. default is en (English)
     */
    public function __construct(string $taxonomy, string $taxonomyType, string $taxonomyName, string $sourceLang = 'en')
    {
        $this->taxonomy = $taxonomy;
        $this->taxonomyType = $taxonomyType;
        $this->taxonomyName = $taxonomyName;

        $this->sourceLang = $sourceLang;
        $this->sitepressHandler = new SitePress_Wrapper();
    }

    #region variable getters
    public function get_taxonomy_name(): string
    {
        return $this->taxonomyName;
    }

    public function get_taxonomy(): string
    {
        return $this->taxonomy;
    }

    public function get_taxonomy_type(): string
    {

        return $this->taxonomyType;
    }

    public function get_sourcelang(): ?string
    {
        return $this->sourceLang;
    }
    #endregion

    #region crud
    public function create_item(string $name, string $slug, string $description = '', int $parentId = 0)
    {
        $termData =  wp_insert_term($name, $this->taxonomy, array(
            'slug' => $slug,
            'description' => $description,
            'parent' => $parentId
        ));

        if ($termData instanceof \WP_Error) {
            wp_die($termData);
        }

        return $this->get_item_by_id($termData['term_id']);
    }

    /**
     * retrieve all items of type `WP_Term`. use $args to handle settings of this function
     *
     * @param array $args
     * @return WP_Term[]
     */
    public function get_items(array $args = []): array
    {
        $args['taxonomy'] = $this->taxonomy;
        $args['hide_empty'] = false;
        return get_terms($args);
    }

    /**
     * wrapper function for get_term_by function.
     * 
     * wraps around `get_term_by` method. if Sitepress/WPML is active, will work with its logic.
     * this can be disabled by calling this class's `disable_sitepress_get_term_filter` method before calling this method.
     *
     * @param integer $id
     * @return WP_Term|null
     */
    public function get_item_by_id(int $id): ?WP_Term
    {
        $termData = get_term_by('id', $id, $this->taxonomy,);
        if (!$termData) {
            return null;
        }
        return $termData;
    }

    /**
     * wrapper function for get_term_by function.
     * 
     * wraps around `get_term_by` method. if Sitepress/WPML is active, will work with its logic.
     * this can be disabled by calling this class's `disable_sitepress_get_term_filter` method before calling this method.
     *
     * @param string $name
     * @return WP_Term|null
     */
    public function get_item_by_name(string $name): ?WP_Term
    {
        $termData = get_term_by('name', $name, $this->taxonomy);
        if (!$termData) {
            return null;
        }
        return $termData;
    }

    /**
     * wrapper function for get_term_by function.
     * 
     * wraps around `get_term_by` method. if Sitepress/WPML is active, will work with its logic.
     * this can be disabled by calling this class's `disable_sitepress_get_term_filter` method before calling this method.
     *
     * @param string $slug
     * @return WP_Term|null
     */
    public function get_item_by_slug(string $slug): ?WP_Term
    {
        $termData = get_term_by('slug', $slug, $this->taxonomy);

        if (!$termData) {
            return null;
        }
        return $termData;
    }

    public function update_item(WP_Term $term, array $args = []): ?WP_Term
    {
        $termData = wp_update_term($term->term_id, $this->taxonomy, $args);
        if (is_wp_error($termData)) {
            //TODO: implement proper error handling and reporting within batch API calls.
            return null;
        }

        //get fresh version of the term
        return $this->get_item_by_id($term->term_id);
    }

    public function delete_item(WP_Term $term): bool
    {
        $result = wp_delete_term($term->term_id, $this->taxonomy);
        if ($result === true) return true;

        if ($result instanceof \WP_Error) {
            throw new WP_Error_Exception($result);
        }
        if ($result === 0) {
            throw new Invalid_Input_Exception("tried to delete {$this->taxonomyName} {$term->name}, which is a default category and not allowed.");
        }

        return false;
    }
    #endregion

    #region helpers
    public function get_original_translation_id(WP_Term $term): int
    {
        if (is_null($this->sitepressHandler->sitepress)) {
            return -1;
        }
        return $this->sitepressHandler->get_object_id($term->term_id, $this->taxonomyType, false, $this->sourceLang);
    }

    /**
     * get Term array of children of an existing term.
     *
     * @param WP_Term $term
     * @return WP_Term[]
     */
    public function get_children(WP_Term $term): array
    {
        $terms = get_terms(array(
            'taxonomy' => $this->taxonomy,
            'parent' => $term->term_id
        ));
        if (is_wp_error($terms)) throw new \Exception("Fatal error: taxonomy {$this->taxonomy} not found in database");
        return $terms;
    }

    public function unparent_children(WP_Term $term): void
    {
        $children = $this->get_children($term);

        foreach ($children as $child) {
            if ($child->parent === $term->term_id) {
                $this->unparent_term($term);
            }
        }
    }

    public function unparent_term(WP_Term $term): void
    {
        wp_update_term($term->term_id, $this->taxonomy, array(
            'parent' => 0,
            'slug' => $term->slug,
            'description' => $term->description,
        ));
    }

    public function configure_SEO_data(WP_Term $term, SEO_Data $data): void
    {
        // if (!isset($seoData)) return;

        $currentSeoMetaData = get_option('wpseo_taxonomy_meta');

        $currentSeoMetaData[$this->taxonomy][$term->term_id]['wpseo_focuskw'] = $data->get_focus_keyword();
        $currentSeoMetaData[$this->taxonomy][$term->term_id]['wpseo_desc'] = $data->get_description();

        update_option('wpseo_taxonomy_meta', $currentSeoMetaData);
    }

    public function configure_translation(WP_Term $translatedTerm, WP_Term $originalTerm, string $lang): bool
    {
        if (is_null($this->sitepressHandler->sitepress)) {
            return false;
        }

        $wpdb = new PWP_WPDB();

        $taxonomyId = $translatedTerm->term_taxonomy_id;
        $parentTaxonomyId = $originalTerm->term_taxonomy_id;
        $trid = $this->sitepressHandler->get_element_trid($parentTaxonomyId, $this->taxonomyType);

        $sourceLang = $this->sourceLang !== $lang ? $this->sourceLang : null;

        $query = $wpdb->prepare_term_translation_query($lang, $sourceLang, (int)$trid, $this->taxonomyType, $taxonomyId);
        $result = $wpdb->query($query);

        return !$result;
    }

    public function is_slug_in_use(string $slug): bool
    {
        return !is_null($this->get_item_by_slug($slug));
    }

    final public function get_trid(WP_Term $original): int
    {
        if (is_null($this->sitepressHandler->sitepress)) {
            return -1;
        }
        $trid = $this->sitepressHandler->get_element_trid($original->term_id, $this->taxonomyType);
        var_dump($trid);
        return (int)$trid ?: 0;
    }

    final public function change_slug(string $oldSlug, string $newSlug): ?\WP_Term
    {
        $term = $this->get_item_by_slug($oldSlug);
        return $this->update_item($term, array('slug' => $newSlug));
    }
    #endregion

    #region sitepress settings
    public function enable_sitepress_get_term_filter(): void
    {
        $this->sitepressHandler->enable_sitepress_get_term_filter();
    }

    public function disable_sitepress_get_term_filter(): void
    {
        $this->sitepressHandler->disable_sitepress_get_term_filter();
    }
    #endregion
}
