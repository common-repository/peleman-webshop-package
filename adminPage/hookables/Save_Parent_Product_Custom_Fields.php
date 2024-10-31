<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\editor\Product_PIE_Data;
use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use WC_Product_Simple;
use WP_Post;

/**
 * Save parent product custom data added by the plugin.
 */
class Save_Parent_Product_Custom_Fields extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct(
            'woocommerce_process_product_meta',
            'save_variables',
            11,
            2
        );
    }

    public function save_variables(int $postId, WP_Post $post): void
    {
        $product = wc_get_product($postId);
        $editorMeta = new Product_Meta_Data($product);

        if (!isset($product)) {
            error_log("tried to save parameters for product with id {$postId}, but something went wrong");
            return;
        }

        $post = $this->filter_post();
        
        $editorMeta->set_unit_amount((int)$post[Product_Meta_Data::UNIT_AMOUNT] ?: 1)
            ->set_unit_price((float)$post[Product_Meta_Data::UNIT_PRICE] ?: 0.0) // Set default value if not provided
            ->set_unit_code($post[Product_Meta_Data::UNIT_CODE][0])
            ->set_uses_pdf_content(
                isset($post[Product_Meta_Data::USE_PDF_CONTENT_KEY])
            )
            ->set_pdf_size_check_enabled(
                isset($post[Product_Meta_Data::PDF_SIZE_CHECK])
            )
            ->set_pdf_max_pages((int)$post[Product_Meta_Data::PDF_MAX_PAGES_KEY])
            ->set_pdf_min_pages((int)$post[Product_Meta_Data::PDF_MIN_PAGES_KEY])
            ->set_pdf_height((int)$post[Product_Meta_Data::PDF_HEIGHT_KEY])
            ->set_pdf_width((int)$post[Product_Meta_Data::PDF_WIDTH_KEY])
            ->set_price_per_page((float)str_replace(',','.',$post[Product_Meta_Data::PDF_PRICE_PER_PAGE_KEY]))
            ->set_custom_add_to_cart_label(
                esc_attr(sanitize_text_field($post[Product_Meta_Data::CUSTOM_LABEL_KEY]))
            )
            ->set_editor(esc_attr(sanitize_text_field($post[Product_Meta_Data::EDITOR_ID_KEY])))
            ->set_override_thumbnail(isset($post[Product_Meta_Data::OVERRIDE_CART_THUMB]))
            ->set_f2d_article_code($post[Product_Meta_Data::F2D_ARTICLE_CODE][0]);

        if ($product instanceof WC_Product_Simple) {
            $pieData = $editorMeta->pie_data();

            $pieData
                ->set_template_id((string)sanitize_text_field($post[Product_PIE_Data::PIE_TEMPLATE_ID_KEY]))
                ->set_design_id((string)sanitize_text_field($post[Product_PIE_Data::DESIGN_ID_KEY]))
                ->set_color_code((string)sanitize_text_field($post[Product_PIE_Data::COLOR_CODE_KEY]))
                ->set_background_id((string)sanitize_text_field($post[Product_PIE_Data::BACKGROUND_ID_KEY]))
                ->set_uses_image_upload(isset($post[Product_PIE_Data::USE_IMAGE_UPLOAD_KEY]))
                ->set_autofill(isset($post[Product_PIE_Data::AUTOFILL_KEY]))
                ->set_num_pages((int)sanitize_text_field($post[Product_PIE_Data::NUM_PAGES_KEY]))
                ->set_max_images((int)sanitize_text_field($post[Product_PIE_Data::MAX_IMAGES_KEY]))
                ->set_min_images((int)sanitize_text_field($post[Product_PIE_Data::MIN_IMAGES_KEY]))
                ->parse_instruction_array($post);
        }

        $product->save_meta_data();
        $editorMeta->update_meta_data();
        $editorMeta->save_meta_data();
    }

    /**
     * filter expected $_POST
     *
     * @return array|false|null
     */
    private function filter_post()
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        return $post;
    }
}