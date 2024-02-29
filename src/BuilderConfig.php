<?php

declare(strict_types=1);

namespace Duyler\Framework;

readonly class BuilderConfig
{
    public function __construct(
        public string $buildPath = 'build',
    ) {}
}
