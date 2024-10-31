<?php

declare(strict_types=1);

namespace PWP\includes\utilities;

class HTML_Builder
{

    public static function open_div(array $args = []): void
    {
        $classes = isset($args['classes']) ? implode(' ', $args['classes']) : '';
        $id = isset($args['id']) ? $args['id'] : '';
?>
        <div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($classes); ?>">
        <?php
    }

    public static function close_div(): void
    {
        ?>
        </div>
    <?php
    }

    public static function heading(string $text, int $weight = 1, array $classes = []): void
    {
        $tag = 'h' . (min(6, max(1, $weight)));
        $classes = implode(' ', $classes);
    ?>
        <<?php echo esc_attr($tag); ?> class='<?php echo esc_attr($classes); ?>'><?php echo esc_attr($text); ?></<?php echo esc_attr($tag); ?>>
<?php
    }
}
