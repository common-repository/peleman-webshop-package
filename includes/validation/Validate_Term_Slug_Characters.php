<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\wrappers\Term_Data;
use PWP\includes\handlers\services\Term_SVC;
use PWP\includes\utilities\notification\I_Notification;
use PWP\includes\validation\Abstract_Term_Handler;

class Validate_Term_Slug_Characters extends Abstract_Term_Handler
{
    private string $expression;

    public function __construct(Term_SVC $service)
    {
        $this->expression = '/^[a-z0-9_-]+(-[a-z0-9_-]+)*$/';
        parent::__construct($service);
    }

    public function handle(Term_Data $request, I_Notification $notification): bool
    {
        $slug = $request->get_slug();

        if (!preg_match($this->expression, $slug)) {
            $notification->add_error(
                __("Invalid characters in slug", 'Peleman-Webshop-Package'),
                sprintf(
                    __("Slug %s not of valid format. can only have lowercase letters, numbers, dashes, and underscores", 'Peleman-Webshop-Package'),
                    $slug
                )
            );
        }
        return
            $this->handle_next($request, $notification);
    }
}
