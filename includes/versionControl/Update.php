<?php

declare(strict_types=1);

namespace PWP\includes\versionControl;

use PWP\includes\versionControl\VersionNumber;

abstract class Update
{
    private VersionNumber $version;

    public function __construct(string $version)
    {
        $this->version = VersionNumber::from_string($version);
    }

    /**
     * runs internal upgrade logic
     * 
     * @return VersionNumber  returns new version number.
     */
    public abstract function upgrade(): VersionNumber;

    /**
     * runs internal downgrade logic
     *
     * @return void
     */
    public abstract function downgrade(): void;


    final public function is_newer_than(VersionNumber $version): bool
    {
        return $this->version->is_newer_than($version);
    }

    final public function get_version_number(): VersionNumber
    {
        return $this->version;
    }

    final public function compare_version(Update $update): int
    {
        return $this->version->compare($update->version);
    }
}
