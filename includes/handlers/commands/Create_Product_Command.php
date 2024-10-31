<?php

declare(strict_types=1);

namespace PWP\includes\handlers\commands;

use PWP\includes\utilities\PWP_WPDB;
use PWP\includes\editor\Product_IMAXEL_Data;
use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\editor\Product_PIE_Data;
use PWP\includes\utilities\SitePress_Wrapper;

abstract class Create_Product_Command implements I_Command
{
    protected array $data;

    public function __construct(array $productData)
    {
        $this->data = $productData;
    }

    protected function set_product_params_from_data(\WC_Product $product): void
    {
        $product->set_name($this->data['name']);
        $product->set_SKU($this->data['sku']);
        $product->set_regular_price((string)$this->data['regular_price']);
        $product->set_reviews_allowed((bool)($this->data['reviews_allowed'] ?: false));

        $product->set_status($this->data['status'] ?: 'publish');
        $product->set_featured($this->data['featured'] ?: false);
        $product->set_catalog_visibility($this->data['catalog_visibility'] ?: "hidden");
        $product->set_description($this->data['description'] ?: '');
        $product->set_short_description($this->data['short_description'] ?: '');

        $product->set_tax_status($this->data['tax_status'] ?: 'taxable');
        $product->set_tax_class($this->data['tax_class'] ?: '');
        $product->set_sold_individually($this->data['sold_individually'] ?: false);

        $product->set_weight($this->data['weight'] ?: 00.00);
        $product->set_length($this->data['dimensions']['length'] ?: 00.00);
        $product->set_height($this->data['dimensions']['height'] ?: 00.00);
        $product->set_width($this->data['dimensions']['width'] ?: 00.00);

        $product->set_purchase_note($this->data['purchase_note'] ?: '');
        $product->set_menu_order($this->data['menu_order'] ?: -1);

        if (isset($this->data['upsell_skus']))
            $product->set_upsell_ids($this->get_product_ids_by_skus($this->data['upsell_skus']));
        if (isset($this->data['cross_sell_skus']))
            $product->set_cross_sell_ids($this->get_product_ids_by_skus($this->data['cross_sell_skus']));
        if (isset($this->data['tags']))
            $product->set_tag_ids($this->get_tag_ids_by_slugs($this->data['tags']));
        if (isset($this->data['categories']))
            $product->set_category_ids($this->get_category_ids_by_slugs($this->data['categories']));
    }

    protected function get_product_ids_by_skus(array $skus): array
    {
        $ids = [];
        foreach ($skus as $sku) {
            $id = wc_get_product_id_by_sku($sku);
            if (empty($id)) continue;
            $ids[] = $id;
        }

        return $ids;
    }

    final protected function get_tag_ids_by_slugs(array $slugs): array
    {
        $ids = [];
        foreach ($slugs as $slug) {
            $tag = get_term_by('slug', $slug, 'post_tag');
            if (!$tag) continue;
            $ids[] = $tag->term_id;
        }

        return $ids;
    }

    final protected function get_category_ids_by_slugs(array $slugs): array
    {
        $ids = [];
        foreach ($slugs as $slug) {
            $category = get_category_by_slug($slug);
            if (!$category) continue;
            $ids[] = $category->term_id;
        }

        return $ids;
    }

    /**
     * Configure two products as translations with WPML
     *
     * @param \WC_Product $translation
     * @param \WC_Product $original
     * @param string $lang
     * @return boolean returns `false` if WPML is not active, or something went wrong with the translation. Returns 'true' if 
     * operation was successful.
     */
    protected function configure_translation(\WC_Product $translation, \WC_Product $original, string $lang): bool
    {
        if (!class_exists("Sitepress")) return false;

        $spWrapper = new SitePress_Wrapper();
        $wpdb = new PWP_WPDB();

        $id = $translation->get_id();
        $parentId = $original->get_id();

        $trid = $spWrapper->get_element_trid($parentId, 'post_product');

        $sourceLang = 'en';

        $query = $wpdb->prepare_term_translation_query($lang, $sourceLang, (int)$trid, 'post_product', $id);
        $result = $wpdb->query($query);

        return !$result;
    }


    protected function set_editor_id(Product_Meta_Data $meta, ?string $id)
    {
        switch ($id) {
            case 'PIE':
                $meta->set_editor(Product_PIE_Data::MY_EDITOR);
                break;
            default:
                $meta->set_editor('');
                break;
        }
    }

    protected function set_product_pie_data(Product_Meta_Data $meta, array $data): void
    {
        $pieData = $meta->pie_data();
        $pieData
            ->set_template_id($data['template_id'])
            ->set_design_id($data['design_id'] ?: '')
            ->set_min_images($data['min_images'] ?: 0)
            ->set_max_images($data['max_images'] ?: 0)
            ->set_color_code($data['color_code'] ?: '')
            ->set_background_id($data['background_id'] ?: '')
            ->set_format_id($data['format_id'] ?: '')
            ->set_num_pages($data['pages_to_fill'] ?: 0)
            ->set_editor_instructions($data['editor_instructions'] ?: '');
    }

    protected function set_product_pdf_data(Product_Meta_Data $meta, array $data): void
    {
        $meta->set_uses_pdf_content($data['requires_upload'] ?: false)
            ->set_pdf_min_pages($data['min_pages'] ?: 0)
            ->set_pdf_max_pages($data['max_pages'] ?: 0)
            ->set_pdf_width($data['page_width'] ?: 0)
            ->set_pdf_height($data['pdf_height'] ?: 0);
    }

    protected function handle_product_meta_components(\WC_Product $product): void
    {
        /** start product meta data flow */
        $productMeta = new Product_Meta_Data($product);

        $productMeta->set_custom_add_to_cart_label($this->data['add_to_cart_label'] ?: '');

        if (isset($this->data['editor_id'])) {
            $this->set_editor_id($productMeta, $this->data['editor_id']);
        }
        if (isset($this->data['pie_settings'])) {
            $this->set_product_pie_data($productMeta, $this->data['pie_settings']);
        }

        if (isset($this->data['pdf_upload'])) {
            $this->set_product_pdf_data($productMeta, $this->data['pdf_upload']);
        }

        foreach ($this->data['meta_data'] as $meta) {
            $product->set_meta_data([$meta['key'] => $meta['value']]);
        }

        /** UPDATE & SAVE META DATA */
        $productMeta->update_meta_data();
        $productMeta->save_meta_data();
    }
}
