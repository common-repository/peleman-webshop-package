<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

class Enqueue_PDF_JS_Scripts extends Abstract_Action_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct('pwp_Register_PDF_JS_Scripts', 'Register_PDF_JS_Scripts', $priority, 0);
    }

    public function Register_PDF_JS_Scripts(): void
    {
        wp_enqueue_script(
            'pdfjs'
        );

        wp_enqueue_script(
            'pdfworkerjs'
        );

        wp_enqueue_script(
            'pwp-validate-pdf-upload.js'
        );
    }
}
