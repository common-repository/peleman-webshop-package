<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\wrappers\PDF_Upload;
use PWP\includes\hookables\abstracts\Abstract_Ajax_Hookable;
use PWP\includes\utilities\notification\Error_Notice;
use PWP\includes\utilities\notification\Notification;
use PWP\includes\utilities\notification\Success_Notice;
use PWP\includes\validation\File_Validator;
use PWP\includes\validation\Validate_File_Dimensions;
use PWP\includes\validation\Validate_File_Errors;
use PWP\includes\validation\Validate_File_PageCount;
use PWP\includes\validation\Validate_File_Size;
use PWP\includes\validation\Validate_File_Type_Is_PDF;
use Smalot\PdfParser\Parser;

/**
 * Ajax hookable for handling and validating a pdf upload from the webshop.
 */
class Ajax_Upload_PDF extends Abstract_Ajax_Hookable
{
    public function __construct()
    {
        parent::__construct(
            'Upload_PDF',
            plugins_url('../js/upload-content.js', __FILE__),
            6,
        );
    }

    public function callback(): void
    {
        /** 1) */
        $this->validate_request_nonce(sanitize_key($_REQUEST['nonce']));

        /** 2) */
        $file = new PDF_Upload($_FILES['file']);
        $productId = (int)sanitize_key($_REQUEST['variant_id'] ?: $_REQUEST['product_id']);
        $product = wc_get_product($productId);
        $productMeta = new Product_Meta_Data($product);

        //check if product Id leads to a valid product
        if (!$product) {
            $this->send_json_error(
                'invalid product id',
                'no product with this product ID passed',
                200,
                array('id' => $productId)
            );
        }

        $notification = new Notification();

        /** 3) */
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($file->get_tmp_name());
            $pages = $pdf->getPages();
            $file->set_page_count(count($pages));

            $page = $pages[0];
            $mediaBox = $page->getDetails()['MediaBox'];

            $height = $mediaBox[2];
            $width = $mediaBox[3];
            $file->set_dimensions($width, $height);
        } catch (\Throwable $error) {
            $this->send_json_error(
                $error->getMessage(),
                '',
                200
            );
        }
        if (!$this->validation_chain($productMeta)->handle($file, $notification)) {
            $error = $notification->get_errors()[0];
            $this->send_json_error(
                $error->get_message(),
                $error->get_description(),
                420
            );
        }

        /** 4) */
        $id = $this->generate_content_file_id($productId);
        // $path = $this->save_file($id, 'content');
        // error_log($path);
        // $this->generate_thumbnail($path, $id, 160);


        //calculate prices

        $priceVatExcl = wc_get_price_excluding_tax($product);
        $priceVatIncl = wc_get_price_including_tax($product);
        $pricePerPage = $productMeta->get_price_per_page();

        $priceWithPages = $pricePerPage * $file->get_page_count() + $priceVatExcl;
        $this->send_json_success(
            'success',
            'success! you have successfully reached the end of the operation chain!',
            200,
            array(
                'file' => array(
                    'price_vat_incl'    => $priceWithPages,
                    'name'              => 'bleep',
                    'content_file_id'   => '123456789bbb',
                )
            )
        );
    }

    public function callback_nopriv(): void
    {
        $this->callback();
    }

    private function generate_content_file_id(int $productId): string
    {
        return sprintf(
            "%u_%u_%s",
            get_current_user_id(),
            base64_encode((string)(microtime(true) * 1000)),
            $productId
        );
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
                5
            ));
        }

        return $validator;
    }

    private function validate_request_nonce(string $nonce): void
    {
        if (!$this->verify_nonce($nonce)) {
            $error = new Error_Notice(
                'nonce mismatch',
                'could not verify origin of request',
            );
            wp_send_json_error($error->to_array(), 401);
        }
    }

    private function send_json_error(string $message, string $description, int $httpCode = 400, array $data = []): void
    {
        $error = new Error_Notice(
            __($message, 'Peleman-Webshop-Package'),
            __($description, 'Peleman-Webshop-Package'),
            $data
        );
        wp_send_json_error($error->to_array(), $httpCode);
    }

    private function send_json_success(string $message, string $description, int $httpCode = 200, array $data = []): void
    {
        $success = new Success_Notice(
            __($message, 'Peleman-Webshop-Package'),
            __($description, 'Peleman-Webshop-Package'),
            $data
        );
        wp_send_json_success($success->to_array(), $httpCode);
    }
}
