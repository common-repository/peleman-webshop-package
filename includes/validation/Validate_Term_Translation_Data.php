<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\wrappers\Term_Data;
use PWP\includes\utilities\notification\I_Notification;

class Validate_Term_Translation_Data extends Abstract_Term_Handler
{
    public function handle(Term_Data $request, I_Notification $notification): bool
    {
        if ($request->has_translation_data()) {
            $translationData = $request->get_translation_data();
            if (!$this->service->is_slug_in_use($translationData->get_english_slug())) {
                $notification->add_error(
                    __("Translation original not found", 'Peleman-Webshop-Package'),
                    sprintf(
                        __("Translation data for term with slug %s does not have a valid or existing english parent slug.", 'Peleman-Webshop-Package'),
                        $request->get_slug()
                    )
                );
            }
            if (!$translationData->get_language_code()) {
                $notification->add_error(
                    __("Language code missing", 'Peleman-Webshop-Package'),
                    sprintf(
                        __("Translation data for term with slug %s lacks a language code", 'Peleman-Webshop-Package'),
                        $request->get_slug()
                    )
                );
            }
        }

        return $this->handle_next($request, $notification);
    }
}
