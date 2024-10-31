<?php

declare(strict_types=1);

namespace PWP\includes\utilities;

class Admin_Notice
{
    private const DISMISSIBLE = 'is-dismissible';
    private const ERROR = 'notice-error';
    private const WARNING = 'notice-warning';
    private const SUCCESS = 'notice-success';
    private const INFO = 'notice-info';

    private string $content;

    private function __construct(string $class, string $content, bool $dismissible = false)
    {
        $class = esc_attr($class . " " . $this->dismiss($dismissible));
        $content = esc_html(__($content, 'myFirstPlugin'));

        $this->content = "<div class='$class'><p>{$content}</p></div>";
    }

    public static function new_error_notice(string $content, bool $dismissible = false): self
    {
        return new Admin_Notice("notice " . self::ERROR, $content, $dismissible);
    }

    public static function new_warning_notice(string $content, bool $dismissible = false): self
    {
        return new Admin_Notice("notice " . self::WARNING, $content, $dismissible);
    }

    public static function new_success_notice(string $content, bool $dismissible = false): self
    {
        return new Admin_Notice("notice " . self::SUCCESS, $content, $dismissible);
    }

    public static function new_info_notice(string $content, bool $dismissible = false): self
    {
        return new Admin_Notice("notice " . self::INFO, $content, $dismissible);
    }

    public function get_content(): string
    {
        return $this->content;
    }

    private function dismiss(bool $dismissible): string
    {
        return $dismissible ? self::DISMISSIBLE : "";
    }
}