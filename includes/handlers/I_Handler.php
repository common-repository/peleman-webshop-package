<?php

declare(strict_types=1);

namespace PWP\includes\handlers;
/**
 * Undocumented class
 * 
 * @deprecated version
 */
interface I_Handler
{
    public function create_item(array $createData, array $args = []): object;

    public function get_item(int $id, array $args = []): ?object;
    public function get_items(array $args = []): array;

    /**
     * Undocumented function
     *
     * @param integer $id id of the item that is to be updated
     * @param array $updateData array of data to update/override the original data
     * @param array $args array of additonal arguments for the update process
     * @param boolean $useNullValues default false. determines if values that have been left empty in the args
     * should be persisted anyway.
     * @return object
     */
    public function update_item(int $id, array $updateData, array $args = [], bool $useNullValues = false): object;

    public function delete_item(int $id, array $args = []): bool;
}
