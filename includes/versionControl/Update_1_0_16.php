<?php

declare(strict_types=1);

namespace PWP\includes\versionControl;

class Update_1_0_16 extends Update
{
    public function __construct()
    {
        parent::__construct('1.0.16');
    }

    public function upgrade(): VersionNumber
    {
        $nr = (string)$this->get_version_number();
        error_log("running Peleman Webshop Plugin update {$nr}");
        if (!is_dir(PWP_UPLOAD_DIR)) {
            mkdir(PWP_UPLOAD_DIR, 0755, true);
        }
        return $this->get_version_number();
    }

    public function downgrade(): void
    {
    }
}
