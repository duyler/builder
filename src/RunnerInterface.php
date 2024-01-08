<?php

declare(strict_types=1);

namespace Duyler\Framework;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\EventBus\BusInterface;

interface RunnerInterface
{
    public const string NAME = 'default';

    public function load(LoaderServiceInterface $loaderService): void;

    public function prepare(BusInterface $bus): void;
}
