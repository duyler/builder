<?php

declare(strict_types=1);

namespace Duyler\Builder;

use Duyler\Builder\Config\PackagesConfig;
use Duyler\DI\ContainerInterface;
use Duyler\EventBus\Build\Action;
use Duyler\EventBus\Build\Event;
use Duyler\EventBus\Build\Trigger;
use Duyler\EventBus\Build\Context;
use Duyler\EventBus\Build\SharedService;
use Duyler\EventBus\BusInterface;
use Duyler\EventBus\Contract\State\StateHandlerInterface;
use Duyler\EventBus\BusBuilder as EventBusBuilder;

final class BusBuilder
{
    public function __construct(
        private EventBusBuilder $busBuilder,
        private ContainerInterface $container,
    ) {}

    public function addSharedService(object $object, array $bind = [], array $providers = []): BusBuilder
    {
        $this->busBuilder->addSharedService(
            new SharedService(
                class: $object::class,
                service: $object,
                bind: $bind,
                providers: $providers,
            ),
        );
        return $this;
    }

    public function addStateHandler(StateHandlerInterface $stateHandler): BusBuilder
    {
        $this->busBuilder->addStateHandler($stateHandler);
        return $this;
    }

    public function addStateContext(Context $context): BusBuilder
    {
        $this->busBuilder->addStateContext($context);

        return $this;
    }

    public function addEvent(Event $event): BusBuilder
    {
        $this->busBuilder->addEvent($event);

        return $this;
    }

    public function addAction(Action $action): BusBuilder
    {
        $this->busBuilder->addAction($action);
        return $this;
    }

    public function doAction(Action $action): BusBuilder
    {
        $this->busBuilder->doAction($action);
        return $this;
    }

    public function addTrigger(Trigger $trigger): BusBuilder
    {
        $this->busBuilder->addTrigger($trigger);
        return $this;
    }

    public function loadPackages(?PackagesConfig $packagesConfig = null): BuildLoader
    {
        $packageLoader = new PackageLoader($this->busBuilder, $this->container);

        return $packageLoader->loadPackages($packagesConfig);
    }

    public function build(): BusInterface
    {
        return $this->busBuilder->build();
    }
}
