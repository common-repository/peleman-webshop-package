<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use DOMDocument;
use DOMXPath;
use PWP\includes\editor\Keys;
use PWP\includes\editor\Product_Meta_Data;
use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;

/**
 * Overrides item thumbnail in cart for customizable products
 */
class Change_Cart_Item_Thumbnail extends Abstract_Filter_Hookable
{
    private ?DOMDocument $dom;
    public function __construct(int $priority = 15)
    {
        parent::__construct('woocommerce_cart_item_thumbnail', 'pwp_override_cart_item_thumbnail', $priority, 3);
        // $this->add_hook('woocommerce_cart_item_name', $priority);
        // $this->add_hook('woocommerce_order_item_name', $priority);
        $this->dom = null;
    }

    public function pwp_override_cart_item_thumbnail(string $image, array $cart_item, $cart_item_key): string
    {
        if (!isset($cart_item['_project_id'])) return $image;
        $projectId = $cart_item['_project_id'];
        $product = $cart_item['data'];

        if (boolval($product->get_meta(Product_Meta_Data::OVERRIDE_CART_THUMB))) {

            if (!$this->dom) {
                $this->dom = new DOMDocument();
            }

            $this->dom->loadHTML($image);
            $x = new DOMXPath($this->dom);

            foreach ($x->query("//img") as $node) {

                $classes = $node->getAttribute("class");
                $src = $node->getAttribute("src");
                $node->setAttribute("class", "{$classes} pwp-fetch-thumb");
                $node->setAttribute("projid", $projectId);
                $node->setAttribute("srcset", site_url() . "/wp-json/pwp/v1/thumb/{$projectId}");
                $node->setAttribute("onerror", "this.onerror='';this.src='{$src}';this.srcset='';");
                // $node->setAttribute("src", site_url() . "/wp-json/pwp/v1/thumb/{$projectId}");
            }
            $image = $this->dom->saveHTML();
        }
        return $image;
    }
}
