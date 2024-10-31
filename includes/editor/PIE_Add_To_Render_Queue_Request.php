<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use PWP\includes\exceptions\Invalid_Response_Exception;

/**
 * PIE request to add project to rendering queue.
 */
class PIE_Add_To_Render_Queue_Request extends Abstract_PIE_Request
{
    private string $projectId;
    private string $orderId;
    private string $outputType;

    public function __construct(Editor_Auth_Provider $auth)
    {
        parent::__construct($auth, '/editor/api/addtoqueueAPI.php');
        $this->projectId = '';
        $this->orderId = '';
        $this->outputType = 'print';
    }

    public function set_project_id(string $id): self
    {
        $this->projectId = $id;
        return $this;
    }

    public function set_order_id(string $id): self
    {
        $this->orderId = $id;
        return $this;
    }

    public function set_output_type(string $type): self
    {
        $this->outputType = $type;
        return $this;
    }

    protected function generate_request_body(): array
    {
        $request = array(
            'customerid'        => $this->get_customer_id(),
            'customerapikey'    => $this->get_api_key(),
            'projectid'         => $this->projectId,
            "orderid"           => $this->orderId,
            'outputtype'        => $this->outputType,
            'type'              => 'default',
        );
        return $request;
    }
}
