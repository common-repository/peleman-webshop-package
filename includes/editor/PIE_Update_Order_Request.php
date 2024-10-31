<?php

declare(strict_types=1);

namespace PWP\includes\editor;

class PIE_Update_Order_Request extends Abstract_PIE_Request
{
    private int $orderId;
    private array $orderLines;

    public function __construct(int $orderId)
    {
        $endpoint = '/editor/api/updateordernr.php';
        $auth = new Editor_Auth_Provider();
        parent::__construct(
            $auth,
            $endpoint
        );

        $this->orderId = $orderId;
        $this->orderLines = [];
        $this->set_POST();
    }

    public function add_order_line(string $orderLineId, string $projectId): self
    {
        $this->orderLines[$orderLineId] = $projectId;
        return $this;
    }

    protected function generate_request_body(): array
    {
        $response = array(
            'order_id' => (string)$this->orderId,
            'order_lines' => [],
        );

        foreach ($this->orderLines as $line => $id) {
            $response['order_lines'][] = array(
                'order_line' => (string)$line,
                'project_id' => $id,
            );
        }

        error_log(json_encode($response));
        return $response;
    }
}
