<?php

declare(strict_types=1);

namespace Duyler\Framework\Build;

use Duyler\EventBus\BusBuilder;
use Duyler\EventBus\Dto\Subscription as SubscriptionDto;
use Duyler\EventBus\Enum\ResultStatus;

class Subscription
{
    private static BusBuilder $busBuilder;

    public function __construct(BusBuilder $busBuilder)
    {
        static::$busBuilder = $busBuilder;
    }

    public static function add(string $subjectId, string $actionId, ResultStatus $status = ResultStatus::Success): void
    {
        self::$busBuilder->addSubscription(
            new SubscriptionDto(
                subjectId: $subjectId,
                actionId: $actionId,
                status: $status,
            )
        );
    }
}
