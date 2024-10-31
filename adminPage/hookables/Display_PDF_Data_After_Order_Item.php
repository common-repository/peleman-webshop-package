<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

/**
 * Add PDF data + download button to an order line item containing a PDF upload.
 */
class Display_PDF_Data_After_Order_Item extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_after_order_itemmeta', 'display_pdf_line', 10, 2);
        $this->add_hook('woocommerce_order_item_meta_end');
    }

    public function display_pdf_line(int $item_id, \WC_Order_Item $item): void
    {
        $nonce = wp_create_nonce('wp_rest');
        if (!($item->get_meta('_pdf_data'))) return;
        $data = $item->get_meta('_pdf_data');
        $id = $data['id'];
        $name = $data['pdf_name'];

        $download = home_url('wp-json/pwp/v1/pdf/' . $id . "?_wpnonce={$nonce}");
?>
        <div><a type="button" class="button" style="margin-top:4px; margin-bottom:4px;" download="<?php echo esc_attr($name); ?>" target="_blank" href="<?php echo esc_url($download); ?>">Download <?php echo esc_html($name); ?></a></div>
<?php
    }
}
