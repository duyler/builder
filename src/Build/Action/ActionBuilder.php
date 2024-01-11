<?php

declare(strict_types=1);

namespace Duyler\Framework\Build\Action;

use Duyler\EventBus\BusBuilder;
use Duyler\Framework\Build\AttributeHandlerCollection;
use Duyler\Framework\Build\AttributeInterface;

class ActionBuilder
{
    /** @var Action[] */
    private array $actions = [];
    public function __construct(
        private BusBuilder $busBuilder,
        private AttributeHandlerCollection $attributeHandlerCollection,
    ) {}

    public function addAction(Action $action): self
    {
        $this->actions[] = $action;
        return $this;
    }

    public function build(): void
    {
        foreach ($this->actions as $action) {
            $busAction = new \Duyler\EventBus\Dto\Action(
                id: $action->get('id'),
                handler: $action->get('handler'),
                required: $action->get('require'),
                bind: $action->get('bind'),
                providers: $action->get('providers'),
                argument: $action->get('argument'),
                contract: $action->get('contract'),
                rollback: $action->get('rollback'),
                externalAccess: $action->get('externalAccess'),
                repeatable: $action->get('repeatable'),
                continueIfFail: $action->get('continueIfFail'),
                private: $action->get('private'),
                sealed: $action->get('sealed'),
                silent: $action->get('silent'),
            );

            /** @var AttributeInterface $attribute */
            foreach ($action->get('attributes') as $attribute) {
                $attributeHandlers = $this->attributeHandlerCollection->get($attribute::class);
                foreach ($attributeHandlers as $attributeHandler) {
                    $attribute->accept($attributeHandler, $busAction);
                }
            }

            $this->busBuilder->addAction($busAction);
        }
    }
}
