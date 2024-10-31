<?php

declare(strict_types=1);

namespace PWP\includes\F2D;

use WP_Post;

interface I_Meta_Property
{
    /**
     * get type string of parent object
     *
     * @return object
     */
    public function get_parent(): object;
    
    /**
     * persist all contained meta data to the database.
     *
     * @return void
     */
    public function update_meta_data(): void;
}
