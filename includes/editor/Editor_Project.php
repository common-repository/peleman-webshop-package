<?php

declare(strict_types=1);

namespace PWP\includes\editor;

/**
 *  base class for editor projects
 */
abstract class Editor_Project
{
    private string $editorId;
    private string $projectId;

    public const EDITOR_ID_KEY = '_editor_id';
    public const PROJECT_ID_KEY = '_project_id';
    public const PROJECT_URL_KEY = '_project_url';

    public function __construct(string $editorId, string $projectId)
    {
        $this->editorId = $editorId;
        $this->projectId = $projectId;
    }

    public function get_project_id(): string
    {
        return $this->projectId;
    }

    public function get_editor_id(): string
    {
        return $this->editorId;
    }

    public abstract function get_project_editor_url(bool $skipUpload = false, $organisation_key): string;

    public function to_array(): array
    {
        return array(
            '_editor_id'    => $this->get_editor_id(),
            '_project_id'   => $this->get_project_id(),
            '_project_url'  => $this->get_project_editor_url(),
        );
    }
}
