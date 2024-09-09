<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\Action;

use Duyler\EventBus\BusBuilder;
use Duyler\Builder\Build\AttributeHandlerCollection;
use Duyler\Builder\Build\AttributeInterface;
use Duyler\Builder\Build\BuilderInterface;

class ActionBuilder implements BuilderInterface
{
    /** @var Action[] */
    private array $actions = [];
    private ActionConfigResolver $actionConfigResolver;

    public function __construct(
        private BusBuilder $busBuilder,
    ) {
        $this->actionConfigResolver = new ActionConfigResolver();
    }

    public function addAction(Action $action): self
    {
        $this->actions[] = $action;
        return $this;
    }

    public function build(AttributeHandlerCollection $attributeHandlerCollection): void
    {
        foreach ($this->actions as $action) {
            $actionConfig = $this->actionConfigResolver->resolve($action->get('config'));

            $busAction = new \Duyler\EventBus\Build\Action(
                id: $action->get('id'),
                handler: $action->get('handler'),
                required: $action->get('require'),
                listen: $action->get('listen'),
                bind: $actionConfig->bind,
                providers: $actionConfig->providers,
                definitions: $actionConfig->definitions,
                argument: $action->get('argument'),
                argumentFactory: $action->get('argumentFactory'),
                contract: $action->get('contract'),
                rollback: $action->get('rollback'),
                externalAccess: $action->get('externalAccess'),
                repeatable: $action->get('repeatable'),
                lock: $action->get('lock'),
                private: $action->get('private'),
                sealed: $action->get('sealed'),
                silent: $action->get('silent'),
                alternates: $action->get('alternates'),
                retries: $action->get('retries'),
                labels: $action->get('labels'),
            );

            /** @var AttributeInterface $attribute */
            foreach ($action->get('attributes') as $attribute) {
                $attributeHandlers = $attributeHandlerCollection->get($attribute::class);
                foreach ($attributeHandlers as $attributeHandler) {
                    $attribute->accept($attributeHandler, $busAction);
                }
            }

            $this->busBuilder->addAction($busAction);
        }

        $this->actions = [];
    }
}
