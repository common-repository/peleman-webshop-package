<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\editor\Editor_Auth_Provider;
use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;

class Update_PIE_Project_Return_URL extends Abstract_Action_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct(
            'pwp_update_pie_project_return_url',
            'update_return_url',
            $priority,
            2
        );
    }

    public function update_return_url(string $project_id, string $target_url): void
    {
        $auth = new Editor_Auth_Provider();
        $params = array(
            'projectid' => $project_id,
            'type'      => 'setreturnurl',
            'value'     => $target_url,
        );

        $url = $auth->get_domain() . "/editor/api/projectfileAPI.php?";
        $url .= http_build_query($params);

        $headers = $auth->get_auth_header();
        $headers['RETURN_URL'] = $target_url;

        $result = wp_remote_get(
            $url,
            array('headers' => $headers)
        );

        if (is_wp_error($result)) {
            error_log($result->get_error_message());
        }
    }
}
