<?php

declare(strict_types=1);

namespace Duyler\Framework\Loader;

use Duyler\ActionBus\Build\Action;
use Duyler\ActionBus\Build\Context;
use Duyler\ActionBus\Build\Event;
use Duyler\ActionBus\Build\Subscription;
use Duyler\ActionBus\Contract\State\StateHandlerInterface;
use Duyler\Framework\Build\AttributeHandlerInterface;
use Duyler\Framework\Build\BuilderInterface;

interface LoaderServiceInterface
{
    public function addAction(Action $action): self;

    public function doAction(Action $action): self;

    public function addStateHandler(StateHandlerInterface $stateHandler): self;

    public function addSharedService(object $service, array $bind = []): self;

    public function addSubscription(Subscription $subscription): self;

    public function addAttributeHandler(AttributeHandlerInterface $attributeHandler): self;

    public function addBuilder(BuilderInterface $builder): self;

    public function addStateContext(Context $context): self;

    public function addEvent(Event $event): self;
}
