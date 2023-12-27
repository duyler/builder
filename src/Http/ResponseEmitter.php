<?php

declare(strict_types=1);

namespace Duyler\Framework\Http;

use Psr\Http\Message\ResponseInterface;

class ResponseEmitter
{
    public function __construct(private ResponseInterface $response) {}

    public function emit(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
