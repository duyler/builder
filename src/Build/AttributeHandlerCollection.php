<?php

declare(strict_types=1);

namespace Duyler\Builder\Build;

use RuntimeException;

class AttributeHandlerCollection
{
    /** @var array<string, AttributeHandlerInterface[]> */
    private array $handlers = [];

    public function addHandler(AttributeHandlerInterface $handler): void
    {
        foreach ($handler->getAttributeClasses() as $attributeClass) {
            $this->handlers[$attributeClass][] = $handler;
        }
        $this->handlers[] = $handler;
    }

    /** @return AttributeHandlerInterface[] */
    public function get(string $attributeClass): array
    {
        return $this->handlers[$attributeClass]
            ?? throw new RuntimeException('Handler for attribute class ' . $attributeClass . ' not found');
    }
}
