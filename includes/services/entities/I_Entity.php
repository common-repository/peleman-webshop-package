<?php

declare(strict_types=1);

namespace PWP\includes\services\entities;

interface I_Entity
{
    /**
     * Find object in database by ID/Primary Key
     *
     * @param integer $id
     * @return object|null
     */
    public static function get_by_id(int $id): ?object;
    /**
     * Save object in database if new, or update if not
     *
     * @return void
     */
    public function persist(): void;
}
