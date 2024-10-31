<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\validation\Validate_File_Size;

/**
 * Enqueues the required PDF.js files for in-browser previewing of pdf uploads.
 */
class Register_PDF_JS_Scripts extends Abstract_Action_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct(
            'wp_enqueue_scripts',
            'pwp_Register_PDF_JS_Scripts',
            $priority
        );
    }

    public function pwp_Register_PDF_JS_Scripts(): void
    {
        $plugin = 'WP_Peleman_Products_Extender';

        wp_register_script(
            'pdfjs',
            plugins_url($plugin . '/vendor/clean-composer-packages/pdf-js/build/pdf.js'),
        );

        wp_register_script(
            'pdfworkerjs',
            plugins_url($plugin . '/vendor/clean-composer-packages/pdf-js/build/pdf.js'),
        );

        wp_register_script(
            'pwp-validate-pdf-upload.js',
            plugins_url($plugin . '/publicPage/js/pwp-validate-pdf-upload.js'),
            array(
                'pdfjs',
                'pdfworkerjs',
                'jquery',
                'wp-i18n',
            ),
            wp_rand(0, 2000),
        );
        wp_localize_script(
            'pwp-validate-pdf-upload.js',
            'validate_pdf',
            array(
                'type_error' => __('Incorrect file type.', 'peleman-webshop-package'),
                'size_error' => __('File is too large and cannot be uploaded.', 'peleman-webshop-package'),
                'max_size'   => (int)ini_get('upload_max_filesize') * Validate_File_Size::MB,
            )
        );

        wp_set_script_translations('pwp-validate-pdf-upload.js', 'peleman-webshop-package');
    }
}
