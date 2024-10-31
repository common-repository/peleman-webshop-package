<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\editor\Product_Meta_Data;

class Display_Project_Data_In_Cart extends Abstract_Action_Hookable
{
    public function __construct()
    {
        parent::__construct('woocommerce_after_cart_item_name', 'render_projectName', 15, 2);
    }

    public function render_projectName(array $cart_item, string $cart_item_key)
    {
        // Check if '_project_id' index exists in $cart_item array
        if (isset($cart_item['_project_id'])) {
            $product = $cart_item['data'];
            $pagesCount = $this->get_pageCount($cart_item);
            $meta = new Product_Meta_Data($product);

            if (isset($cart_item['_project_reference'])) {
                $name = $cart_item['_project_reference'];
            } else {
                $name = '';
                $location = get_option('pie_domain', '');
                $location .= '/editor/api/projectfileAPI.php?action=get&projectid=' . $cart_item['_project_id'] . '&a=' . get_option('pie_api_key');
                $response = wp_remote_request($location);
                $projectJson = json_decode($response['body']);
                if ($projectJson) {          
                    $cart_item['_project_reference'] = $projectJson->name;
                    $name = $projectJson->name;
                }
            }

            ?>
            <div style='font-size: 12px;' class='cartReference'><b>Project Reference:</b> <?php echo esc_html($name); ?></div>
            <?php
            
            // New API call and calculations printing the results on the screen
            $pagesCount = $this->get_pageCount($cart_item);
            $freePages = $meta->get_page_amount();
            $pricePerExtraPage = $meta->get_cover_price_per_page();
            $currencySymbol = get_woocommerce_currency_symbol();
            $unitAmount = $meta->get_unit_amount();
            $unitPrice = $meta->get_unit_price();

            $pagesToPay = $pagesCount - $freePages;

            if ($unitPrice !== 0 && $pagesToPay > 0) {
                $extraPricePerBook = $pricePerExtraPage * $pagesToPay;
                $totalExtraPrice = $extraPricePerBook * $unitAmount;
            } else {
                $totalExtraPrice = $pricePerExtraPage * $pagesToPay;
            }

            if ($pagesCount > $freePages) {
                echo "<div style='font-size: 12px;' class='pagesCount'><b>Total Pages:</b> <span style='color: red;'>" . esc_html($pagesCount) . "</span> / " . esc_html($freePages) . "</div>";
                echo "<div style='font-size: 12px;' class='pagesFee'><b>Additional pages cost:</b> " . wc_price($extraPricePerBook) ."</div>";
            }

            if ($pagesToPay > 0 && $unitAmount > 1) {
                echo "<div style='font-size: 12px;' class='pagesFee'><b>Total additional cost:</b> " . wc_price($totalExtraPrice) . "</div>";
            }
        }
    }

    public function get_pageCount(array $cartItem)
    {
        $locationForPages = get_option('pie_domain', '') . '/editor/api/projectAPI.php?action=getpages&projectid=' . $cartItem['_project_id'] . '&a=' . get_option('pie_api_key');
        $response = wp_remote_request($locationForPages);
        $projectJson = json_decode($response['body']);
        $pagesCount = $projectJson->pagesCount ?? null;
        return $pagesCount;
    }
}
