<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\Trigger;

use Duyler\EventBus\Build\Trigger as TriggerDto;
use Duyler\EventBus\BusBuilder;
use Duyler\EventBus\Enum\ResultStatus;
use UnitEnum;

class Trigger
{
    private static BusBuilder $busBuilder;

    public function __construct(BusBuilder $busBuilder)
    {
        static::$busBuilder = $busBuilder;
    }

    public static function add(
        string|UnitEnum $subjectId,
        string|UnitEnum $actionId,
        ResultStatus $status = ResultStatus::Success,
    ): void {
        self::$busBuilder->addTrigger(
            new TriggerDto(
                subjectId: $subjectId,
                actionId: $actionId,
                status: $status,
            ),
        );
    }
}
