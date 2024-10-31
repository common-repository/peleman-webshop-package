<?php

declare(strict_types=1);

namespace PWP\adminPage\hookables;

use PWP\includes\editor\Editor_Auth_Provider;
use PWP\includes\editor\PIE_Verify_Credentials_Request;
use PWP\includes\hookables\abstracts\Abstract_Ajax_Hookable;

class Ajax_Verify_PIE_Editor_Credentials extends Abstract_Ajax_Hookable
{
    public function __construct(int $priority = 10)
    {
        parent::__construct(
            'Ajax_verify_pie_credentials',
            plugins_url('..\js\pwp_test_editor_credentials.js', __FILE__),
            $priority
        );

        $this->set_admin(true);
    }

    public function callback(): void
    {
        $auth = new Editor_Auth_Provider();
        $auth->new_credentials(
            $_POST['domain'],
            $_POST['api_key'],
            $_POST['customer_id'],

        );
        $request = new PIE_Verify_Credentials_Request($auth);

        $response = $request->make_request();
        $code = (int)$response->response->code ?: 0;
        $message = $this->get_response_message($code);
        wp_send_json_success(
            array(
                'responseCode' => $code,
                'message' => $message
            ),
            200
        );
    }

    public function callback_nopriv(): void
    {
        wp_send_json_error('Not allowed', 401);
    }

    private function get_response_message(int $code)
    {
        error_log("editor response code: {$code}");
        if ($code < 200) {
            return __('Response not recognized.', 'Peleman-Webshop-Package');
        }
        if ($code >= 200 && $code < 300) {
            return  __('Credentials OK.', 'Peleman-Webshop-Package');
        }
        if ($code >= 400 && $code < 500) {
            return  __('Credentials Invalid', 'Peleman-Webshop-Package');
        }
        if ($code >= 500) {
            return  __('Server error; Try again later.', 'Peleman-Webshop-Package');
        }
        return  __('Invalid response received; Try again later.', 'Peleman-Webshop-Package');
    }
}
