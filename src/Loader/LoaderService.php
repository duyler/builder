<?php

declare(strict_types=1);

namespace Duyler\Builder\Loader;

use Duyler\ActionBus\Build\Action;
use Duyler\ActionBus\Build\Context;
use Duyler\ActionBus\Build\Event;
use Duyler\ActionBus\Build\SharedService;
use Duyler\ActionBus\Build\Subscription;
use Duyler\ActionBus\BusBuilder;
use Duyler\ActionBus\Contract\State\StateHandlerInterface;
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

    public function addSubscription(Subscription $subscription): self
    {
        $this->busBuilder->addSubscription($subscription);
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
