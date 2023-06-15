<?php

declare(strict_types=1);

namespace Duyler\Framework\Loader;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\DependencyInjection\ContainerInterface;
use Duyler\EventBus\BusBuilder;

readonly class LoaderService implements LoaderServiceInterface
{
    public function __construct(
        private ContainerInterface $container,
        private BusBuilder         $busBuilder,
    ) {
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getBuilder(): BusBuilder
    {
        return $this->busBuilder;
    }
}
