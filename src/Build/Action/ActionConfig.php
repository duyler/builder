<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\Action;

readonly class ActionConfig
{
    public function __construct(
        public array $bind,
        public array $providers,
        public array $definitions,
    ) {}
}
