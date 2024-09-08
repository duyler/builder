<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\Event;

use Duyler\EventBus\BusBuilder;
use UnitEnum;

class Event
{
    private static BusBuilder $busBuilder;

    public function __construct(BusBuilder $busBuilder)
    {
        static::$busBuilder = $busBuilder;
    }

    public static function add(
        string|UnitEnum $id,
        ?string $contract = null,
    ): void {
        self::$busBuilder->addEvent(
            new \Duyler\EventBus\Build\Event(
                id: $id,
                contract: $contract,
            ),
        );
    }
}
