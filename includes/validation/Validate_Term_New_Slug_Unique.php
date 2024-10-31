<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\wrappers\Term_Data;
use PWP\includes\utilities\notification\I_Notification;

class Validate_Term_New_Slug_Unique extends Abstract_Term_Handler
{
    public function handle(Term_Data $request, I_Notification $notification): bool
    {
        $slug = $request->get_new_slug();

        if (!is_null($slug) && $this->service->is_slug_in_use($slug)) {
            $notification->add_error(
                __("Slug not unique", 'Peleman-Webshop-Package'),
                sprintf(__("Slug %s is already in use for a %s term", 'Peleman-Webshop-Package'), $slug, $this->service->get_taxonomy_name())
            );
        }
        return $this->handle_next($request, $notification);
    }
}
