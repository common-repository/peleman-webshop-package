<?php

declare(strict_types=1);

namespace PWP\publicPage\hookables;

use PWP\includes\hookables\abstracts\Abstract_Action_Hookable;
use PWP\includes\services\entities\Project;

/**
 * Cron job hookable for cleaning up old and unorderdered projects.
 * Will try to clean up abandoned and unordered projects files from the system.
 * Default cutoff date for abandoned projects is 30 days, but can also be configured with the `pwp_project_cleanup_cutoff_days` WP option
 * CAUTION: cron jobs are only called publically and should thus be registered as a public hook.
 */
class Cleanup_Unordered_Projects extends Abstract_Action_Hookable
{
    public const HOOK = 'pwp_cleanup_projects';
    public function __construct()
    {
        parent::__construct(self::HOOK, 'clean');

        if (!wp_next_scheduled(self::HOOK)) {
            wp_schedule_event(time(), 'daily', self::HOOK);
        }
    }

    public function clean(): void
    {
        if (!boolval(get_option('pwp_cleanup_projects'))) {
            return;
        }
        
        error_log("project cleanup job running...");
        $days = (int)(get_option('pwp_project_cleanup_cutoff_days') ?: 30);
        $cutoffDate = current_time("timestamp") - ($days * 86400);
        // error_log("cutoff timestamp: " . print_r($cutoffDate, true));

        $counter = 0;
        $projects = Project::get_all_unordered_projects();

        foreach ($projects as $project) {
            $lastUpdate = $project->get_updated()->getTimestamp();
            error_log(print_r($lastUpdate, true));
            if ($lastUpdate <= $cutoffDate) {
                $counter++;
                $project->delete_files();
                $project->delete();
            }
        }

        if (0 < $counter) {
            error_log("deleted {$counter} projects from system.");
            return;
        }
        error_log("no projects deleted.");
    }
}
