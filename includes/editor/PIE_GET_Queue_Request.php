<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use PWP\includes\exceptions\Invalid_Response_Exception;

class PIE_GET_Queue_Request extends Abstract_PIE_Request
{
    private string  $status;
    private string  $type;
    private string  $outputtype;
    private int     $maxresults;
    private string  $sort;
    private string  $orderId;
    private string  $scheduleDate;

    private string $projectId;

    public function __construct(Editor_Auth_Provider $auth)
    {
        $endpoint = '/editor/api/getqueues.php';
        parent::__construct($auth, $endpoint);

        $this->status = '';
        $this->type = '';
        $this->outputtype = '';
        $this->maxresults = 0;
        $this->sort = '';
        $this->orderId = '';
        $this->scheduleDate = '';

        $this->projectId = '';

        $this->set_GET();
    }

    public static function new(Editor_Auth_Provider $auth): self
    {
        return new self($auth);
    }

    public function set_status(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function set_type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function set_output_type(string $type): self
    {
        $this->outputtype = $type;
        return $this;
    }

    public function set_max_results(int $max): self
    {
        $this->maxresults = $max;
        return $this;
    }

    public function set_sort(string $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    public function set_orderId(string $id): self
    {
        $this->orderId = $id;
        return $this;
    }

    public function set_schedule_date(string $date): self
    {
        $this->scheduleDate = $date;
        return $this;
    }
    public function set_project_id(string $projectId): self
    {
        $this->projectId = $projectId;
        return $this;
    }
    protected function generate_request_body(): array
    {
        $query = array(
            'customerapikey'    => $this->get_api_key(),
            'customerid'        => $this->get_customer_id(),
            'status'            => $this->status,
            'type'              => $this->type,
            'outputtype'        => $this->outputtype,
            'maxresults'        => $this->maxresults,
            'sort'              => $this->sort,
            'orderid'           => $this->orderId,
            'projectid'         => $this->projectId,
        );

        return array_filter($query);
    }

    protected function generate_request_header(): array
    {
        return array();
    }

    public function make_request(): object
    {
        $response = wp_remote_get($this->request_url());
        $response = $response['body'];

        if (empty($response) || is_bool($response)) {
            throw new Invalid_Response_Exception('No valid response received. Likely an authentication issue. Try again later.');
        }
        $response = json_decode($response, true);
        return (object)$response;
    }

    public function request_url(): string
    {
        $query = '?' . http_build_query($this->generate_request_body());
        return $this->get_endpoint_url() . $query;
    }
}
