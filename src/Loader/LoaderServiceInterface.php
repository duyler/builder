<?php

declare(strict_types=1);

namespace Duyler\Builder\Loader;

use Duyler\ActionBus\Build\Action;
use Duyler\ActionBus\Build\Context;
use Duyler\ActionBus\Build\Event;
use Duyler\ActionBus\Build\SharedService;
use Duyler\ActionBus\Build\Subscription;
use Duyler\ActionBus\Contract\State\StateHandlerInterface;
use Duyler\Builder\Build\AttributeHandlerInterface;
use Duyler\Builder\Build\BuilderInterface;

interface LoaderServiceInterface
{
    public function addAction(Action $action): self;

    public function doAction(Action $action): self;

    public function addStateHandler(StateHandlerInterface $stateHandler): self;

    public function addSharedService(SharedService $service): self;

    public function addSubscription(Subscription $subscription): self;

    public function addAttributeHandler(AttributeHandlerInterface $attributeHandler): self;

    public function addBuilder(BuilderInterface $builder): self;

    public function addStateContext(Context $context): self;

    public function addEvent(Event $event): self;
}
