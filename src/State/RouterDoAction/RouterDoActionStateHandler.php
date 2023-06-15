<?php

declare(strict_types=1);

namespace Duyler\Framework\State\RouterDoAction;

use Duyler\EventBus\Contract\State\StateMainAfterHandlerInterface;
use Duyler\EventBus\Enum\ResultStatus;
use Duyler\EventBus\State\Service\StateMainAfterService;
use Duyler\Router\Result;

class RouterDoActionStateHandler implements StateMainAfterHandlerInterface
{
    public function handle(StateMainAfterService $stateService): void
    {
        /** @var Result $result */
        $result = $stateService->resultData;

        if ($stateService->resultStatus === ResultStatus::Success && $result->status) {
            $stateService->doExistsAction($result->handler . '.' . $result->action);
        }
    }

    public function observed(): array
    {
        return ['Router.StartRouting'];
    }

    public function prepare(): void
    {
        // TODO: Implement prepare() method.
    }
}
