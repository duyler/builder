<?php

declare(strict_types=1);

namespace Duyler\Framework\Build;

interface AttributeHandlerInterface
{
    /** @return string[] */
    public function getAttributeClasses(): array;
}
