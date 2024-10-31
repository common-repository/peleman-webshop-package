<?php

declare(strict_types=1);

namespace PWP\includes\menus;

interface IWPMenu
{
    public function render_menu(string $page_slug): void;
}
