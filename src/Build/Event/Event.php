<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\Event;

use Duyler\EventBus\BusBuilder;
use UnitEnum;

final class Event
{
    private static BusBuilder $busBuilder;

    public function __construct(BusBuilder $busBuilder)
    {
        static::$busBuilder = $busBuilder;
    }

    public static function declare(
        string|UnitEnum $id,
        ?string $type = null,
        bool $immutable = true,
    ): void {
        self::$busBuilder->addEvent(
            new \Duyler\EventBus\Build\Event(
                id: $id,
                type: $type,
                immutable: $immutable,
            ),
        );
    }
}
