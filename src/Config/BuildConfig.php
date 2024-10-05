<?php

declare(strict_types=1);

namespace Duyler\Builder\Config;

readonly class BuildConfig
{
    public function __construct(
        public array $buildPaths = ['build'],
    ) {}
}
