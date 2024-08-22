<?php

declare(strict_types=1);

namespace Duyler\Builder\Build;

interface BuilderInterface
{
    public function build(AttributeHandlerCollection $attributeHandlerCollection): void;
}
