<?php

declare(strict_types=1);

namespace Duyler\Builder\Build;

interface AttributeInterface
{
    public function accept(AttributeHandlerInterface $handler, mixed $item): void;
}
