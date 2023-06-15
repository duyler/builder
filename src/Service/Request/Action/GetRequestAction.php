<?php

declare(strict_types=1);

namespace Duyler\Framework\Service\Request\Action;

use Symfony\Component\HttpFoundation\Request;

class GetRequestAction
{
    private Request $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    public function __invoke(): Request
    {
        return $this->request;
    }
}
