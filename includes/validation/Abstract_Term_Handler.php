<?php

declare(strict_types=1);

namespace PWP\includes\validation;

use PWP\includes\handlers\services\Term_SVC;
use PWP\includes\utilities\notification\I_Notification;
use PWP\includes\wrappers\Term_Data;

abstract class Abstract_Term_Handler
{
    private ?Abstract_Term_Handler $next;
    protected Term_SVC $service;

    public function __construct(Term_SVC $service)
    {
        $this->next = null;
        $this->service = $service;
    }

    /**
     * set next link in chain of responsibility
     *
     * @param Abstract_Term_Handler $next
     * @return Abstract_Term_Handler
     */
    final public function set_next(Abstract_Term_Handler $next): Abstract_Term_Handler
    {
        $this->next = $next;
        return $this->next;
    }

    abstract public function handle(Term_Data $request, I_Notification $notification): bool;

    final protected function handle_next(Term_Data $request, I_Notification $notification): bool
    {
        return is_null($this->next)
            ? $notification->is_success()
            : $this->next->handle($request, $notification);
    }
}
