<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;
use PWP\includes\utilities\notification\Notification;
use PWP\includes\utilities\pdfHandling\PDFI_PDF_Factory;
use PWP\includes\validation\File_Validator;
use PWP\includes\validation\Validate_File_Dimensions;
use PWP\includes\validation\Validate_File_Errors;
use PWP\includes\validation\Validate_File_PageCount;
use PWP\includes\validation\Validate_File_Size;
use PWP\includes\validation\Validate_File_Type_Is_PDF;

/**
 * Validates pdf upload by customer in the woocommerce product validation chain
 */
class Validate_PDF_Upload extends Abstract_Filter_Hookable
{
    private string $key;
    public function __construct()
    {
        parent::__construct('woocommerce_add_to_cart_validation', 'validate_pdf_upload', 10, 5);
        $this->key = "pdf-upload";
    }

    public function validate_pdf_upload(bool $passed, int $product_id, int $quantity, int $variation_id = 0, array $variations = []): bool
    {
        $product = new Product_Meta_Data(wc_get_product($variation_id ?: $product_id));

        if (!$product->uses_pdf_content()) return $passed;


        if (!isset($_FILES[$this->key]) || $_FILES[$this->key]['error'] == 4) {
            wc_add_notice(
                __('Error: Product requires PDF upload.', 'Peleman-Webshop-Package'),
                'error'
            );
            return false;
        }

        if (!isset($_FILES[$this->key]['error']) || is_array($_FILES[$this->key]['error'])) {
            error_log(print_r($_FILES[$this->key]['error'], true));
            wc_add_notice(
                __('Error: Invalid file upload. Try again with a different file.', 'Peleman-Webshop-Package'),
                'error'
            );
            return false;
        }

        try {

            $pdfFactory = new PDFI_PDF_Factory();
            $pdf = $pdfFactory->generate_from_upload($_FILES[$this->key]);

            $notification = new Notification();
            $this->validation_chain($product)->handle($pdf, $notification);

            if (!$notification->is_success()) {
                wc_add_notice(
                    $notification->get_errors()[0]->get_description() ?: __('Error: The uploaded pdf is not valid.', 'Peleman-Webshop-Package'),
                    'error'
                );
            }

            return $notification->is_success() ? $passed : false;
        } catch (\Exception $e) {
            error_log((string)$e);
            $this->save_pdf_for_review();
            return false;
        } catch (\Error $e) {
            error_log((string)$e);
            $this->save_pdf_for_review();
            return false;
        }
    }

    /**
     * generate and return an iterator chain that validates a file
     *
     * @return File_Validator
     */
    private function validation_chain(Product_Meta_Data $metaData): File_Validator
    {
        $maxFileSize = (int)ini_get('upload_max_filesize') * Validate_File_Size::MB;

        $validator = new Validate_File_Type_Is_PDF();
        $validator
            ->set_next(new Validate_File_Errors())
            ->set_next(new Validate_File_Size($maxFileSize))
            ->set_next(new Validate_File_PageCount(
                $metaData->get_pdf_min_pages(),
                $metaData->get_pdf_max_pages()
            ));
        if (get_option('pwp_global_pdf_size_validation', true) && $metaData->pdf_size_check_enabled()) {
            $validator->set_next(new Validate_File_Dimensions(
                $metaData->get_pdf_height(),
                $metaData->get_pdf_width(),
                $metaData->get_pdf_margin_error(),

            ));
        }

        return $validator;
    }

    private function save_pdf_for_review(): void
    {
        $file = $_FILES[$this->key];
        $upload = wp_handle_upload($_FILES[$this->key]);
        error_log("saving pdf for testing purposes: " . $upload['url']);
        wc_add_notice(
            __('Error: Could not process PDF upload. Please try again with a different file.', 'Peleman-Webshop-Package'),
            'error'
        );
    }
}
