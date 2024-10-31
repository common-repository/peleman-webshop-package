<?php

declare(strict_types=1);

namespace PWP\includes\versionControl;

use Exception;
use wpdb;
use PWP\includes\versionControl\Update;
use PWP\includes\versionControl\VersionNumber;

class VersionController
{
    /**
     * version of the current plugin, and the version number the version controller is trying to reach
     */
    private VersionNumber $pluginVersion;
    /**
     * version number of the local version, and what we're trying to upgrade
     */
    private VersionNumber $localVersion;

    /**
     * @var Update[]
     */
    private array $updates;

    public function __construct(string $pluginVersion, string $localVersion)
    {
        $this->pluginVersion = VersionNumber::from_string($pluginVersion);
        $this->localVersion = VersionNumber::from_string($localVersion);
        $this->updates = array();
    }

    public function try_update()
    {
        if ($this->pluginVersion->is_newer_than($this->localVersion)) {
            $this->register_updates();
            $this->upgrade_to_newest_version();
        }
    }

    private function register_updates(): void
    {
        //here we register all the update objects
        //this way we ensure we only load and register these objects when we are trying to upgrade the local version

        // $this->add_upgrade(new ExampleUpdate('0.0.3'));
        $this->add_upgrade(new Update_1_0_16());


        //just to be sure, sort array of updates by version number (from oldest to newest);
        uasort($this->updates, function (Update $a, Update $b) {
            return $a->compare_version($b);
        });
    }

    private function add_upgrade(Update $update): void
    {
        $this->updates[] = $update;
    }

    private function upgrade_to_newest_version(): void
    {
        $latestVersion = $this->localVersion;
        $key = 0;
        try {
            foreach ($this->updates as $key => $update) {
                $latestVersion = $this->run_update($update, $latestVersion);
            }
        } catch (\Exception $error) {
            //undo last upgrade we tried to do
            $this->updates[$key]->downgrade();

            //throw error back into the wild
            throw $error;
        }

        //when all updates have been performed successfully, update the local plugin version to the current version
        update_option('pwp-version', (string)$this->pluginVersion);
    }

    private function run_update(Update $update, VersionNumber $latestVersion): VersionNumber
    {
        if ($update->is_newer_than($latestVersion)) {
            return $update->upgrade();
        }
        return $latestVersion;
    }
}
