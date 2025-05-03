<?php

declare(strict_types=1);

namespace Duyler\Builder\Test\Unit;

use Duyler\Builder\BusBuilder;
use Duyler\Builder\Config\PackagesConfig;
use Duyler\DI\ContainerInterface;
use Duyler\EventBus\Build\Action;
use Duyler\EventBus\Build\Context;
use Duyler\EventBus\Build\Event;
use Duyler\EventBus\Build\SharedService;
use Duyler\EventBus\Build\Trigger;
use Duyler\EventBus\BusBuilder as EventBusBuilder;
use Duyler\EventBus\BusInterface;
use Duyler\EventBus\Contract\State\StateHandlerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BusBuilderTest extends TestCase
{
    private BusBuilder $busBuilder;
    private EventBusBuilder|MockObject $eventBusBuilder;
    private ContainerInterface|MockObject $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventBusBuilder = $this->createMock(EventBusBuilder::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->busBuilder = new BusBuilder($this->eventBusBuilder, $this->container);
    }

    #[Test]
    public function should_add_shared_service(): void
    {
        $service = new \stdClass();
        $bind = ['TestInterface' => 'TestImplementation'];
        $providers = ['TestProvider'];

        $this->eventBusBuilder->expects($this->once())
            ->method('addSharedService')
            ->with($this->callback(function (SharedService $sharedService) use ($service, $bind, $providers) {
                return $sharedService->class === $service::class
                    && $sharedService->service === $service
                    && $sharedService->bind === $bind
                    && $sharedService->providers === $providers;
            }));

        $result = $this->busBuilder->addSharedService($service, $bind, $providers);
        $this->assertSame($this->busBuilder, $result);
    }

    #[Test]
    public function should_add_state_handler(): void
    {
        $stateHandler = $this->createMock(StateHandlerInterface::class);

        $this->eventBusBuilder->expects($this->once())
            ->method('addStateHandler')
            ->with($stateHandler);

        $result = $this->busBuilder->addStateHandler($stateHandler);
        $this->assertSame($this->busBuilder, $result);
    }

    #[Test]
    public function should_add_state_context(): void
    {
        $context = new Context(['test' => 'value']);

        $this->eventBusBuilder->expects($this->once())
            ->method('addStateContext')
            ->with($context);

        $result = $this->busBuilder->addStateContext($context);
        $this->assertSame($this->busBuilder, $result);
    }

    #[Test]
    public function should_add_event(): void
    {
        $event = new Event('TestEvent');

        $this->eventBusBuilder->expects($this->once())
            ->method('addEvent')
            ->with($event);

        $result = $this->busBuilder->addEvent($event);
        $this->assertSame($this->busBuilder, $result);
    }

    #[Test]
    public function should_add_action(): void
    {
        $action = new Action(
            id: 'TestAction',
            handler: function () {},
            required: ['RequiredAction'],
            type: null
        );

        $this->eventBusBuilder->expects($this->once())
            ->method('addAction')
            ->with($action);

        $result = $this->busBuilder->addAction($action);
        $this->assertSame($this->busBuilder, $result);
    }

    #[Test]
    public function should_do_action(): void
    {
        $action = new Action(
            id: 'TestAction',
            handler: function () {},
            required: ['RequiredAction'],
            type: null
        );

        $this->eventBusBuilder->expects($this->once())
            ->method('doAction')
            ->with($action);

        $result = $this->busBuilder->doAction($action);
        $this->assertSame($this->busBuilder, $result);
    }

    #[Test]
    public function should_add_trigger(): void
    {
        $trigger = new Trigger(
            'TestAction',
            'TestEvent'
        );

        $this->eventBusBuilder->expects($this->once())
            ->method('addTrigger')
            ->with($trigger);

        $result = $this->busBuilder->addTrigger($trigger);
        $this->assertSame($this->busBuilder, $result);
    }

    //#[Test]
    //public function should_load_packages(): void
    //{
    //    $packageLoader = $this->createMock(\Duyler\Builder\Loader\PackageLoaderInterface::class);
    //    $attributeHandlerCollection = $this->createMock(\Duyler\Builder\Build\AttributeHandlerCollection::class);
    //    $builderCollection = $this->createMock(\Duyler\Builder\Build\BuilderCollection::class);
    //
    //    $this->container->expects($this->exactly(3))
    //        ->method('get')
    //        ->willReturnCallback(function ($class) use ($packageLoader, $attributeHandlerCollection, $builderCollection) {
    //            return match ($class) {
    //                PackagesConfig::class => $this->packagesConfig,
    //                \Duyler\Builder\Build\AttributeHandlerCollection::class => $attributeHandlerCollection,
    //                \Duyler\Builder\Build\BuilderCollection::class => $builderCollection,
    //                'TestPackageLoader' => $packageLoader,
    //                default => null,
    //            };
    //        });
    //
    //    $packageLoader->expects($this->once())
    //        ->method('load')
    //        ->with($this->isInstanceOf(\Duyler\Builder\Loader\LoaderService::class));
    //
    //    $buildLoader = $this->busBuilder->loadPackages();
    //
    //    $this->assertInstanceOf(\Duyler\Builder\BuildLoader::class, $buildLoader);
    //}

    #[Test]
    public function should_build_bus(): void
    {
        $bus = $this->createMock(BusInterface::class);

        $this->eventBusBuilder->expects($this->once())
            ->method('build')
            ->willReturn($bus);

        $result = $this->busBuilder->build();

        $this->assertSame($bus, $result);
    }
} 
