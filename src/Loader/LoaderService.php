<?php

declare(strict_types=1);

namespace Duyler\Framework\Loader;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\EventBus\BusBuilder;
use Duyler\EventBus\Contract\State\StateHandlerInterface;
use Duyler\EventBus\Dto\Action;
use Duyler\EventBus\Dto\Subscription;
use Duyler\Framework\Build\AttributeHandlerCollection;
use Duyler\Framework\Build\AttributeHandlerInterface;

readonly class LoaderService implements LoaderServiceInterface
{
    public function __construct(
        private BusBuilder $busBuilder,
        private AttributeHandlerCollection $attributeHandlerCollection,
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
}
