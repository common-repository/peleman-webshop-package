<?php

declare(strict_types=1);

namespace PWP\templates;

class Template
{
    public $templateDirectory;

    public function __construct(string $directory = null)
    {
        if ($directory) {
            $this->set_directory($directory);
        }
    }

    public function set_directory(string $directory): void
    {
        $this->templateDirectory = rtrim($directory);
    }

    public function render(string $template, array $vars = []): void
    {
        $template = "{$this->templateDirectory}/{$template}.php";
        echo $this->render_template($template, $vars);
    }


    private function render_template(string $template, array $vars = []): string
    {
        ob_start();
        foreach ($vars as $key => $value) {
            ${$key} = $value;
        }
        include $template;
        return ob_get_clean();
    }
}
