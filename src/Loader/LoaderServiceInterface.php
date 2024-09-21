<?php

declare(strict_types=1);

namespace Duyler\Builder\Loader;

use Duyler\EventBus\Build\Action;
use Duyler\EventBus\Build\Context;
use Duyler\EventBus\Build\Event;
use Duyler\EventBus\Build\SharedService;
use Duyler\EventBus\Build\Trigger;
use Duyler\EventBus\Contract\State\StateHandlerInterface;
use Duyler\Builder\Build\AttributeHandlerInterface;
use Duyler\Builder\Build\BuilderInterface;

interface LoaderServiceInterface
{
    public function addAction(Action $action): self;

    public function doAction(Action $action): self;

    public function addStateHandler(StateHandlerInterface $stateHandler): self;

    public function addSharedService(SharedService $service): self;

    public function addSubscription(Trigger $subscription): self;

    public function addAttributeHandler(AttributeHandlerInterface $attributeHandler): self;

    public function addBuilder(BuilderInterface $builder): self;

    public function addStateContext(Context $context): self;

    public function addEvent(Event $event): self;
}
