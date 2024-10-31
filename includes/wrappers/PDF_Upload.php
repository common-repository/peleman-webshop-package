<?php

declare(strict_types=1);

namespace PWP\includes\wrappers;

class PDF_Upload extends Component
{
    private int $pageCount;
    private float $height;
    private float $width;

    private string $contentId;
    private string $location;


    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->pageCount = 0;
        $this->height = 0;
        $this->width = 0;

        $this->contentId = '';
        $this->location = '';
    }
    public function get_name(): string
    {
        return sanitize_file_name($this->data->name);
    }

    public function get_type(): string
    {
        return $this->data->type;
    }

    public function get_tmp_name(): string
    {
        return ($this->data->tmp_name);
    }

    public function get_error(): int
    {
        return (int)$this->data->error;
    }

    /**
     * return file size in bytes
     *
     * @return integer
     */
    public function get_size(): int
    {
        return (int)$this->data->size;
    }

    public function set_page_count(int $pages): void
    {
        $this->pageCount = $pages;
    }

    public function get_page_count(): int
    {
        return $this->pageCount;
    }

    public function set_dimensions(float $width, float $height): void
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function get_height(): float
    {
        return $this->height;
    }

    public function get_width(): float
    {
        return $this->width;
    }

    public function set_content_id(string $id): void
    {
        $this->contentId = $id;
    }

    public function get_content_id(): string
    {
        return $this->contentId;
    }

    public function set_file_location(string $location): void
    {
        $this->location = $location;
    }
    public function get_file_location(): string
    {
        return $this->location;
    }

    public function to_array(): array
    {
        return array(
            'name'              => $this->get_name(),
            'content_file_id'   => $this->get_content_id(),
            'location'          => $this->get_file_location(),
            'fileSize'          => $this->get_size(),
            'width'             => $this->get_width(),
            'height'            => $this->get_height(),
            'pages'             => $this->get_page_count(),
        );
    }
}
