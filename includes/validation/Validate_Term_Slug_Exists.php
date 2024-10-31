<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\wrappers\Term_Data;
use PWP\includes\utilities\notification\I_Notification;

class Validate_Term_Slug_Exists extends Abstract_Term_Handler
{
    public function handle(Term_Data $request, I_Notification $notification): bool
    {
        $slug = $request->get_slug() ?: null;
        if (empty($slug) || !$this->service->is_slug_in_use($slug)) {
            $notification->add_error(
                __("Term not found", 'Peleman-Webshop-Package'),
                sprintf(
                    __("term %s with slug %s does not exist", 'Peleman-Webshop-Package'),
                    $this->service->get_taxonomy_name(),
                    $slug
                )
            );
        }
        return $this->handle_next($request, $notification);
    }
}
