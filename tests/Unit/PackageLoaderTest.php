<?php

declare(strict_types=1);

namespace Duyler\Builder\Test\Unit;

use Duyler\Builder\Build\AttributeHandlerCollection;
use Duyler\Builder\Build\BuilderCollection;
use Duyler\Builder\Config\PackagesConfig;
use Duyler\Builder\Loader\LoaderService;
use Duyler\Builder\Loader\PackageLoaderInterface;
use Duyler\Builder\PackageLoader;
use Duyler\DI\ContainerInterface;
use Duyler\EventBus\BusBuilder as EventBusBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PackageLoaderTest extends TestCase
{
    private PackageLoader $packageLoader;
    private EventBusBuilder|MockObject $busBuilder;
    private ContainerInterface|MockObject $container;
    private PackagesConfig $packagesConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->busBuilder = $this->createMock(EventBusBuilder::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->packageLoader = new PackageLoader($this->busBuilder, $this->container);
        $this->packagesConfig = new PackagesConfig(['TestPackageLoader']);
    }

    //#[Test]
    //public function should_load_packages(): void
    //{
    //    $packageLoader = $this->createMock(PackageLoaderInterface::class);
    //    $attributeHandlerCollection = $this->createMock(AttributeHandlerCollection::class);
    //    $builderCollection = $this->createMock(BuilderCollection::class);
    //
    //    $this->container->expects($this->exactly(3))
    //        ->method('get')
    //        ->willReturnCallback(function ($class) use ($packageLoader, $attributeHandlerCollection, $builderCollection) {
    //            return match ($class) {
    //                PackagesConfig::class => $this->packagesConfig,
    //                AttributeHandlerCollection::class => $attributeHandlerCollection,
    //                BuilderCollection::class => $builderCollection,
    //                'TestPackageLoader' => $packageLoader,
    //                default => null,
    //            };
    //        });
    //
    //    $packageLoader->expects($this->once())
    //        ->method('load')
    //        ->with($this->isInstanceOf(LoaderService::class));
    //
    //    $buildLoader = $this->packageLoader->loadPackages();
    //
    //    $this->assertInstanceOf(\Duyler\Builder\BuildLoader::class, $buildLoader);
    //}

    #[Test]
    public function should_build_bus(): void
    {
        $bus = $this->createMock(\Duyler\EventBus\BusInterface::class);

        $this->busBuilder->expects($this->once())
            ->method('build')
            ->willReturn($bus);

        $result = $this->packageLoader->build();

        $this->assertSame($bus, $result);
    }

    #[Test]
    public function should_use_custom_packages_config(): void
    {
        $packageLoader = $this->createMock(PackageLoaderInterface::class);
        $attributeHandlerCollection = $this->createMock(AttributeHandlerCollection::class);
        $builderCollection = $this->createMock(BuilderCollection::class);
        $customConfig = new PackagesConfig(['CustomPackageLoader']);

        $this->container->expects($this->exactly(3))
            ->method('get')
            ->willReturnCallback(function ($class) use ($packageLoader, $attributeHandlerCollection, $builderCollection) {
                return match ($class) {
                    AttributeHandlerCollection::class => $attributeHandlerCollection,
                    BuilderCollection::class => $builderCollection,
                    'CustomPackageLoader' => $packageLoader,
                    default => null,
                };
            });

        $packageLoader->expects($this->once())
            ->method('load')
            ->with($this->isInstanceOf(LoaderService::class));

        $buildLoader = $this->packageLoader->loadPackages($customConfig);

        $this->assertInstanceOf(\Duyler\Builder\BuildLoader::class, $buildLoader);
    }
}
