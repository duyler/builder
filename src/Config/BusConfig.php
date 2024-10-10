<?php

declare(strict_types=1);

namespace Duyler\Builder\Config;

use Duyler\DI\Definition;
use Duyler\EventBus\Enum\Mode;

readonly class BusConfig
{
    public function __construct(
        /** @var array<string, string> */
        public array $bind = [],

        /** @var array<string, string> */
        public array $providers = [],

        /** @var Definition[] */
        public array $definitions = [],
        public bool $allowSkipUnresolvedActions = true,
        public bool $autoreset = false,
        public bool $allowCircularCall = false,
        public int $logMaxSize = 50,
        public Mode $mode = Mode::Queue,
        public bool $continueAfterException = false,
    ) {}
}
