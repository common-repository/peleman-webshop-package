<?php

declare(strict_types=1);

namespace PWP\includes;

use PWP\publicPage\hookables\Cleanup_Unordered_Projects;

defined('ABSPATH') || exit;

/**
 * deactivator class for the Peleman Product API plugin
 * is to be run when the plugin is deactivated from the Wordpress admin menu
 */
class Deactivator
{
    public static function deactivate()
    {
        error_log("disabling PWP plugin...");

        self::unschedule_cron_jobs(Cleanup_Unordered_Projects::HOOK);
    }

    private static function unschedule_cron_jobs(...$cronHooks): void
    {
        foreach ($cronHooks as $hook) {
            self::unschedule_cron_job($hook);
        }
    }

    private static function unschedule_cron_job(string $cronhook): void
    {
        while (wp_next_scheduled($cronhook)) {
            $timestamp = wp_next_scheduled($cronhook);
            wp_unschedule_event($timestamp, $cronhook);
        }
    }
}
