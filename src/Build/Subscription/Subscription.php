<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\Subscription;

use Duyler\EventBus\Build\Subscription as SubscriptionDto;
use Duyler\EventBus\BusBuilder;
use Duyler\EventBus\Enum\ResultStatus;
use UnitEnum;

class Subscription
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
        self::$busBuilder->addSubscription(
            new SubscriptionDto(
                subjectId: $subjectId,
                actionId: $actionId,
                status: $status,
            ),
        );
    }
}
