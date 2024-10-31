<?php

declare(strict_types=1);

namespace PWP\includes;

use PWP\includes\utilities\response\I_Response;

abstract class Abstract_Request
{
    protected int $timeout = 30;
    protected int $redirects = 5;

    final public function set_timeout(int $seconds): void
    {
        $this->timeout = $seconds;
    }
    final public function set_redirects(int $redirects): void
    {
        $this->redirects = $redirects;
    }
    public abstract function make_request(): object;
}
