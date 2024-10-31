<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

/**displays the name of the uploaded PDF in the cart */
class Display_PDF_Data_In_Cart extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_after_cart_item_name', 'render_pdf_title', 15, 2);
    }

    public function render_pdf_title(array $cart_item, string $cart_item_key)
    {
        if (isset($cart_item['_pdf_data'])) {
            $id = $cart_item['_pdf_data']['id'];
            $filename = $cart_item['_pdf_data']['pdf_name'];
            $pages = $cart_item['_pdf_data']['pages'];

?>
            <div style='font-size: 12px;'><strong>PDF</strong>: <?php echo esc_html($filename); ?></div>
            <div style='font-size: 12px;'><strong>PDF Pages</strong>: <?php echo esc_html($pages); ?></div>
<?php
        }
    }
}