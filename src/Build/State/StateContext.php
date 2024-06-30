<?php

declare(strict_types=1);

namespace Duyler\Framework\Build\State;

use Duyler\ActionBus\Build\Context;
use Duyler\ActionBus\BusBuilder;

class StateContext
{
    private static BusBuilder $busBuilder;

    public function __construct(BusBuilder $busBuilder)
    {
        self::$busBuilder = $busBuilder;
    }

    /** @param array<array-key, string> $scope */
    public static function add(array $scope): void
    {
        self::$busBuilder->addStateContext(
            new Context(
                $scope,
            ),
        );
    }
}
