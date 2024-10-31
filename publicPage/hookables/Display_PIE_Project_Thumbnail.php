<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

class Display_PIE_Project_Thumbnail extends Abstract_Action_Hookable
{

    public function __construct(int $priority = 10)
    {
        parent::__construct('pwp_display_pie_project_thumbnail', 'display_thumbnail', $priority, 3);
    }

    public function display_thumbnail(string $project_id, array $size = [], array $args = []): void
    {
        $height = isset($size['height']) ? esc_attr($size['height']) : 0;
        $width = isset($size['width']) ? esc_attr($size['width']) : 0;
        $altText = isset($args['alt']) ? esc_attr($args['alt']) : '';
        $classes = isset($args['classes']) ? $args['classes'] : [];

        $sourceImage = site_url() . "/wp-json/pwp/v1/thumb/{$project_id}";
        $sourceSetImage = site_url() . "/wp-json/pwp/v1/thumb/{$project_id}";

        $img = "<img src='" . esc_url($sourceImage) . "' ";
        $img .= "srcset='" . esc_url($sourceSetImage) . "' ";
        $img .= $altText    ? "alt='{$altText}' "   : '';
        $img .= $height     ? "height={$height} " : '';
        $img .= $width      ? "width={$width} " : '';
        $img .= $classes    ? "class='" . esc_attr(implode(' ', $classes)) . "' " : '';
        $img .= '>';

        echo $img;
    }
}
