<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use DOMDocument;
use DOMXPath;
use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;

class Change_Product_Thumbnail_In_Order extends Abstract_Filter_Hookable
{
    private ?DOMDocument $dom;
    public function __construct(int $priority = 10)
    {
        parent::__construct(
            'woocommerce_admin_order_item_thumbnail',
            'override_order_thumbnail',
            $priority,
            3
        );

        $this->dom = null;
    }

    public function override_order_thumbnail(string $image, int $item_id, \WC_Order_Item_Product $item): string
    {
        $projectId = $item->get_meta('_project_id') ?? 0;
        if (empty($projectId)) {
            return $image;
        }
        $this->Init_Dom();

        $this->dom->loadHTML($image);
        $x = new DOMXPath($this->dom);

        $thumbSrc = $this->generate_thumb_url($projectId);

        foreach ($x->query("//img") as $node) {
            $classes = $node->getAttribute("class");
            $src = $node->getAttribute("src");
            $node->setAttribute("class", "{$classes} pwp-fetch-thumb");
            $node->setAttribute("projid", $projectId);
            $node->setAttribute("src", $thumbSrc);
            $node->setAttribute("srcset", $thumbSrc);
            $node->setAttribute("onerror", "this.onerror='';this.src='{$src}';this.srcset='';");
        }

        return $this->dom->saveHTML();
    }

    private function Init_Dom()
    {
        if (!$this->dom) {
            $this->dom = new DOMDocument();
        }
    }

    private function generate_thumb_url(string $projectId): string
    {
        return site_url() . "/wp-json/pwp/v1/thumb/{$projectId}";
    }
}
