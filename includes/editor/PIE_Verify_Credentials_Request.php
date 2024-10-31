<?php

declare(strict_types=1);

namespace PWP\includes\editor;

class PIE_Verify_Credentials_Request extends Abstract_PIE_Request
{
    public function __construct(Editor_Auth_Provider $auth)
    {
        $endpoint = '/editor/api/getcustomerbyid.php';
        parent::__construct(
            $auth,
            $endpoint,
        );
        $this->set_GET();
    }

    protected function generate_request_body(): array
    {
        return [
            'customerId' => $this->get_customer_id(),
        ];
    }
}
