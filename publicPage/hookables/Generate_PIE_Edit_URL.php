<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;

class Generate_PIE_Edit_URL extends Abstract_Filter_Hookable
{
    public function __construct(int $priority = 1)
    {
        parent::__construct('pwp_generate_pie_project_url', 'generate_url', $priority, 3);
    }

    public function generate_url(string $url, string $project_id, array $params = []): string
    {
        $url = get_option('pie_domain', $url);
        $url .= "/editor/upload";
        $url .= "?projectid={$project_id}";

        $url .= '&' . http_build_query($params);
        return $url;
    }
}
