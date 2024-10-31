<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Filter_Hookable;
use PWP\includes\services\entities\Project;

class Get_PDF_File_From_Project extends Abstract_Filter_Hookable
{
    public function __construct(int $priority = 1)
    {
        parent::__construct('pwp_get_project_pdf_data', 'get_pdf_data', $priority, 2);
    }

    public function get_pdf_data(?array $data, int $projectId = 0): ?array
    {
        error_log("project Id: {$projectId}");
        if (!$projectId) {
            $data['error'] = 'invalid project ID';
            error_log($data['error']);
            return $data;
        }
        $project = Project::get_by_id($projectId);
        if (!$project) {
            $data['error'] = 'invalid project ID';
            error_log($data['error']);
            return $data;
        }

        $path = PWP_UPLOAD_DIR . $project->get_path();
        if (!file_exists($path)) {
            $data['error'] = 'PWP Project refers to incorrect file path.';
            error_log($data['error']);
            return $data;
        }
        $name = $project->get_file_name();

        $data = array(
            'path' => $path,
            'name' => $name,
        );

        return $data;
    }
}
