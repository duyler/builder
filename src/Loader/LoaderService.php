<?php

declare(strict_types=1);

namespace Duyler\Framework\Loader;

use Duyler\ActionBus\BusBuilder;
use Duyler\ActionBus\Contract\State\StateHandlerInterface;
use Duyler\ActionBus\Dto\Action;
use Duyler\ActionBus\Dto\Context;
use Duyler\ActionBus\Dto\Subscription;
use Duyler\Framework\Build\AttributeHandlerCollection;
use Duyler\Framework\Build\AttributeHandlerInterface;
use Duyler\Framework\Build\BuilderCollection;
use Duyler\Framework\Build\BuilderInterface;

readonly class LoaderService implements LoaderServiceInterface
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

    public function addSharedService(object $service, array $bind = []): self
    {
        $this->busBuilder->addSharedService($service, $bind);
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
}
