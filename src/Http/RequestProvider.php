<?php

declare(strict_types=1);

namespace Duyler\Framework\Http;

use Psr\Http\Message\ServerRequestInterface;

class RequestProvider
{
    private ServerRequestInterface $request;

    public function get(): ServerRequestInterface
    {
        return $this->request;
    }

    public function set(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}
