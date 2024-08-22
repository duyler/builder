<?php

declare(strict_types=1);

namespace Duyler\Builder\Build;

interface AttributeHandlerInterface
{
    /** @return string[] */
    public function getAttributeClasses(): array;
}
