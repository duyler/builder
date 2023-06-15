<?php

declare(strict_types=1);

namespace Duyler\Framework\Service\Router\Provider;

use Duyler\DependencyInjection\Provider\AbstractProvider;
use Symfony\Component\HttpFoundation\Request;

class RequestProvider extends AbstractProvider
{
    public function __construct(private readonly Request $request)
    {
    }

    public function getParams(): array
    {
        return [
            'uri' => $this->request->getRequestUri(),
            'method' => $this->request->getMethod(),
            'host' => $this->request->getHost(),
            'protocol' => $this->request->isSecure() ? 'https' : 'http',
        ];
    }
}
