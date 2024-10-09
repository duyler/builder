<?php

declare(strict_types=1);

namespace Duyler\Builder;

use Duyler\Builder\Build\AttributeHandlerCollection;
use Duyler\Builder\Build\BuilderCollection;
use Duyler\Builder\Config\PackagesConfig;
use Duyler\Builder\Loader\LoaderService;
use Duyler\Builder\Loader\PackageLoaderInterface;
use Duyler\DI\ContainerInterface;
use Duyler\EventBus\BusBuilder as EventBusBuilder;

class PackageLoader
{
    public function __construct(
        private EventBusBuilder $busBuilder,
        private ContainerInterface $container,
    ) {}

    public function loadPackages(?PackagesConfig $packagesConfig = null): BuildLoader
    {
        /** @var PackagesConfig $packagesConfig */
        $packagesConfig = $packagesConfig ?? $this->container->get(PackagesConfig::class);

        /** @var AttributeHandlerCollection $attributeHandlerCollection */
        $attributeHandlerCollection = $this->container->get(AttributeHandlerCollection::class);

        /** @var BuilderCollection $builderCollection */
        $builderCollection = $this->container->get(BuilderCollection::class);

        $loaderService = new LoaderService(
            $this->busBuilder,
            $attributeHandlerCollection,
            $builderCollection,
        );

        foreach ($packagesConfig->packages as $loaderClass) {
            /** @var PackageLoaderInterface $packageLoader */
            $packageLoader = $this->container->get($loaderClass);
            $packageLoader->load($loaderService);
        }

        return new BuildLoader($this->busBuilder, $this->container);
    }
}
