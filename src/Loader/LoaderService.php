<?php

declare(strict_types=1);

namespace Duyler\Builder\Loader;

use Duyler\EventBus\Build\Action;
use Duyler\EventBus\Build\Context;
use Duyler\EventBus\Build\Event;
use Duyler\EventBus\Build\SharedService;
use Duyler\EventBus\Build\Trigger;
use Duyler\EventBus\BusBuilder;
use Duyler\EventBus\Contract\State\StateHandlerInterface;
use Duyler\Builder\Build\AttributeHandlerCollection;
use Duyler\Builder\Build\AttributeHandlerInterface;
use Duyler\Builder\Build\BuilderCollection;
use Duyler\Builder\Build\BuilderInterface;

final readonly class LoaderService implements LoaderServiceInterface
{
    public function __construct(
        private BusBuilder $busBuilder,
        private AttributeHandlerCollection $attributeHandlerCollection,
        private BuilderCollection $builderCollection,
    ) {}

    public function addAction(Action $action): self
    {
        $this->busBuilder->addAction($action);
        return $this;
    }

    public function doAction(Action $action): self
    {
        $this->busBuilder->doAction($action);
        return $this;
    }

    public function addStateHandler(StateHandlerInterface $stateHandler): self
    {
        $this->busBuilder->addStateHandler($stateHandler);
        return $this;
    }

    public function addSharedService(SharedService $service): self
    {
        $this->busBuilder->addSharedService($service);
        return $this;
    }

    public function addTrigger(Trigger $trigger): self
    {
        $this->busBuilder->addTrigger($trigger);
        return $this;
    }

    public function addAttributeHandler(AttributeHandlerInterface $attributeHandler): self
    {
        $this->attributeHandlerCollection->addHandler($attributeHandler);
        return $this;
    }

    public function addBuilder(BuilderInterface $builder): self
    {
        $this->builderCollection->addBuilder($builder);
        return $this;
    }

    public function addStateContext(Context $context): self
    {
        $this->busBuilder->addStateContext($context);
        return $this;
    }

    public function addEvent(Event $event): self
    {
        $this->busBuilder->addEvent($event);
        return $this;
    }
}
