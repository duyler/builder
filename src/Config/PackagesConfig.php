<?php

declare(strict_types=1);

namespace Duyler\Builder\Config;

readonly class PackagesConfig
{
    public function __construct(
        /** @var string[] $packages */
        public array $packages = [],
    ) {}
}
