<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\wrappers\Term_Data;
use PWP\includes\utilities\notification\I_Notification;

class Validate_Term_Parent_Exists extends Abstract_Term_Handler
{
    public function handle(Term_Data $request, I_Notification $notification): bool
    {
        $parentSlug = $request->get_parent_slug() ?: '';
        if (!empty($parentSlug) && !$this->service->is_slug_in_use($parentSlug)) {
            $notification->add_error(
                __("Parent term not found", 'Peleman-Webshop-Package'),
                sprintf(
                    __("Parent term %s with slug %s does not exist", 'Peleman-Webshop-Package'),
                    $this->service->get_taxonomy_name(),
                    $parentSlug
                )
            );
        }
        return $this->handle_next($request, $notification);
    }
}
