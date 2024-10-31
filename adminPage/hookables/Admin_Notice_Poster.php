<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\utilities\Admin_Notice;

/**
 * Hookable for adding and displaying notices in the admin menu
 */
class Admin_Notice_Poster extends Abstract_Action_Hookable
{
    /**
     * @var Admin_notice[]
     */
    private array $notices;

    public function __construct()
    {
        $this->notices = array();
        parent::__construct('admin_notices', 'display_notices');
    }

    public function display_notices(...$args): void
    {
        foreach ($this->notices as $notice) {
            printf($notice->get_content());
        }
        $this->clear_notices();
    }

    private function add_admin_notice(Admin_Notice $notice): void
    {
        $this->notices[] = $notice;
    }

    public function new_error_notice(string $content, bool $dismissible = false): void
    {
        $this->add_admin_notice(Admin_Notice::new_error_notice($content, $dismissible));
    }

    public function new_info_notice(string $content, bool $dismissible = false): void
    {
        $this->add_admin_notice(Admin_Notice::new_info_notice($content, $dismissible));
    }

    public function new_warning_notice(string $content, bool $dismissible = false): void
    {
        $this->add_admin_notice(Admin_Notice::new_warning_notice($content, $dismissible));
    }

    public function new_success_notice(string $content, bool $dismissible = false): void
    {
        $this->add_admin_notice(Admin_Notice::new_success_notice($content, $dismissible));
    }

    private function clear_notices(): void
    {
        $this->notices = array();
    }
}
