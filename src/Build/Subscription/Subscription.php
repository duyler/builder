<?php

declare(strict_types=1);

namespace Duyler\Framework\Build\Subscription;

use Duyler\ActionBus\BusBuilder;
use Duyler\ActionBus\Dto\Subscription as SubscriptionDto;
use Duyler\ActionBus\Enum\ResultStatus;
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
