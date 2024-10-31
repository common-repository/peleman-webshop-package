<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use WC_Product;

class Add_PDF_Prices_To_Cart extends Abstract_Action_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct('pwp_modify_cart_item_before_calculate_totals', 'add_pdf_costs_to_item', $priority, 1);
    }

    public function add_pdf_costs_to_item(array $cartItem): void
    {
        if (isset($cartItem['_project_id'])) {
            $this->add_pdf_costs_with_project_id($cartItem);
        } else {
            $this->add_pdf_costs_without_project_id($cartItem);
        }
    }

    private function add_pdf_costs_with_project_id(array $cartItem): void
    {
        $product = $cartItem['data'];
        $quantity = $cartItem['quantity'];
        $pagesCount = $this->get_pageCount($cartItem);
        $meta = new Product_Meta_Data($product);

        // Get the original product to reset the price
        $originalProduct = wc_get_product($product->get_id());
        $price = (float)$product->get_meta('cart_price') ?: (float)$originalProduct->get_price();
        $unitAmount = $meta->get_unit_amount();
        $unitPrice = (float)$product->get_meta('unit_price');

        $args = [
            'qty' => 1,
            'price' => $price,
        ];

        // Update product price with pdf price if applicable
        if (isset($cartItem['_pdf_data'])) {
            $pdfData = $cartItem['_pdf_data'];
            if ($pdfData) {
                $pages = $pdfData['pages'];
                $pricePerPage = $meta->get_price_per_page();
                $args['price'] += $pages * $pricePerPage * $unitAmount;
            }
        }

        // Check if customer page number is greater than page amount
        $pageAmount = $meta->get_page_amount(); // Get the total number of pages for a given project.
        $pricePerExtraPage = $meta->get_cover_price_per_page(); // Get the price for each additional page.
        $customerPageNumber = $pagesCount; // The number of pages of the client.

        // If the page quantity is set and the customer's page count is greater than the page quantity:
        if (isset($unitPrice) && $unitPrice !== 0  && $customerPageNumber > $pageAmount) {
            $extraPages = $customerPageNumber - $pageAmount;
            $this->calculate_additional_price($args, $extraPages, $pricePerExtraPage, $unitAmount);
        } else {
            $args['price'];
        }

        if (wc_prices_include_tax()) {
            $price = wc_get_price_including_tax($product, $args);
        } else {
            $price = wc_get_price_excluding_tax($product, $args);
        }

        // Set the calculated price for the product.
        $cartItem['data']->set_price($price);
    }

    private function add_pdf_costs_without_project_id(array $cartItem): void
    {
        $product = $cartItem['data'];
        $quantity = $cartItem['quantity'];
        $meta = new Product_Meta_Data($product);

        // Get the original product to reset the price
        $originalProduct = wc_get_product($product->get_id());
        $price = (float)$product->get_meta('cart_price') ?: (float)$originalProduct->get_price();
        $unitAmount = max((int)$product->get_meta('unit_amount'), 1);

        $args = [
            'qty' => 1,
            'price' => $price,
        ];

        // Update product price with pdf price if applicable
        if (isset($cartItem['_pdf_data'])) {
            $pdfData = $cartItem['_pdf_data'];
            if ($pdfData) {
                $pages = $pdfData['pages'];
                $pricePerPage = $meta->get_price_per_page();
                $args['price'] += $pages * $pricePerPage * $unitAmount;
            }
        }

        if (wc_prices_include_tax()) {
            $price = wc_get_price_including_tax($product, $args);
        } else {
            $price = wc_get_price_excluding_tax($product, $args);
        }

        // Set the calculated price for the product.
        $cartItem['data']->set_price($price);
    }

    public function validate_minimum_quantity(array $cartItem): array
    {
        // Get the product object
        $product = wc_get_product($cartItem['product_id']);

        // Initialize Product_Meta_Data object
        $meta = new Product_Meta_Data($product);

        // Get the minimum quantity set for the product
        $minQuantity = $meta->get_min_quantity();

        // If minimum quantity is set and the quantity added to cart is less than the minimum,
        // adjust the quantity to the minimum quantity
        if ($minQuantity > 0 && $cartItem['quantity'] < $minQuantity) {
            $cartItem['quantity'] = $minQuantity;
        }

        return $cartItem;
    }

    private function calculate_additional_price(array &$args, float $extraPages, float $pricePerExtraPage, float $unitAmount): void
    {
        $additionalPrice = $extraPages * $pricePerExtraPage * $unitAmount;
        $args['price'] += $additionalPrice;
    }

    private function get_pageCount(array $cartItem)
    {
        $locationForPages = get_option('pie_domain', '') . '/editor/api/projectAPI.php?action=getpages&projectid=' . $cartItem['_project_id'] . '&a=' . get_option('pie_api_key');
        $response = wp_remote_request($locationForPages);
        $projectJson = json_decode($response['body']);
        $pagesCount = $projectJson->pagesCount ?? null;
        return $pagesCount;
    }
}
