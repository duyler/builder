<?php

declare(strict_types=1);

namespace Duyler\Builder;

use Duyler\EventBus\Enum\Mode;

readonly class BuilderConfig
{
    public function __construct(
        public string $buildPath = 'build',
        public string $configPath = 'config',
        public ?Mode $overrideBusMode = null,
    ) {}
}
