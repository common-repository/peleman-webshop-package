<?php

declare(strict_types=1);

namespace PWP\includes\menus;

abstract class Admin_Menu implements IWPMenu
{
    protected string $option_group;
    protected string $title;
    protected string $page_slug;

    public function __construct(string $title, string $option_group, string $page_slug)
    {
        $this->title = $title;
        $this->option_group = $option_group;
        $this->page_slug = $page_slug;
    }

    public function get_title(): string
    {
        return $this->title;
    }
    public function render_menu(string $page_slug): void
    {
        settings_fields($this->option_group);
        do_settings_sections($page_slug);
        submit_button();
    }

    public abstract function register_settings(): void;

    final public static function text_property_callback(array $args): void
    {
        $option = $args['option'];
        $value = get_option($option);
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        $description = isset($args['description']) ? $args['description'] : '';

        $classArray = isset($args['classes']) ? $args['classes'] : [];
        $classArray[] = 'regular-text';
        $classes = implode(" ", $classArray);
?>
        <input type="text" id="<?php echo esc_attr($option); ?>" name="<?php echo esc_attr($option); ?>" value="<?php echo esc_html($value); ?>" placeholder="<?php echo esc_html($placeholder); ?>" class="<?php esc_attr($classes); ?>" size=40 />
        <?php
        if ($description) {
            echo wp_kses_post("<p class='description'>{$description}</p>");
        }
    }

    final public static function bool_property_callback(array $args): void
    {
        $option = $args['option'];
        $description = $args['description'] ?: '';
        $value = get_option($option);

        $classArray = isset($args['classes']) ? $args['classes'] : [];
        $classArray[] = 'regular-text';
        $classes = implode(" ", $classArray);

        ?>
        <input type='checkbox' id=" <?php echo esc_attr($option); ?>" name="<?php echo esc_attr($option); ?>" value="1" class="<?php esc_attr($classes); ?>" <?php checked(1, (int)$value, true); ?> />
<?php
        if ($description) {
            echo wp_kses_post("<p class='description'>{$description}</p>");
        }
    }
}
