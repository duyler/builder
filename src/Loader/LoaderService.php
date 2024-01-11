<?php

declare(strict_types=1);

namespace Duyler\Framework\Loader;

use Duyler\Config\ConfigInterface;
use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\DependencyInjection\ContainerInterface;
use Duyler\Framework\Build\Builder;

readonly class LoaderService implements LoaderServiceInterface
{
    public function __construct(
        private ContainerInterface $container,
        private ConfigInterface $config,
        private Builder $builder,
    ) {}

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }
}
