<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;
use PWP\includes\services\entities\Project;

class Get_PDF_Project_Data extends Abstract_Filter_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct('pwp_get_pdf_project_data', 'get_data', $priority, 2);
    }

    public function get_data(array $data, int $id): array
    {
        $project = Project::get_by_id($id);
        if (is_null($project)) {
            return $data;
        }

        $data['path']       = PWP_UPLOAD_DIR . $project->get_path();
        $data['filename']   = $project->get_file_name();
        $data['pages']      = $project->get_pages();

        return $data;
    }
}
