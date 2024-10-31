<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables\editor;

use PWP\includes\editor\Editor_Auth_Provider;
use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

class Set_PIE_Project_Output_Description extends Abstract_Action_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct(
            'pwp_set_pie_project_output_description',
            'set_output_description',
            $priority,
            2
        );
    }

    public function set_output_description(string $project_id, string $description): void
    {
        $auth = new Editor_Auth_Provider();
        $params = array(
            'projectid' => $project_id,
            'type'      => 'setoutputdescription',
            'value'     => $description,
        );

        $url = $auth->get_domain() . "/editor/api/projectfileAPI.php?";
        $url .= http_build_query($params);

        $result = wp_remote_get(
            $url,
            array('headers' => $auth->get_auth_header())
        );

        if (is_wp_error($result)) {
            error_log($result->get_error_message());
        }
    }
}
