<?php

declare(strict_types=1);

namespace Duyler\Framework\Build;

use Closure;
use Duyler\EventBus\BusBuilder;
use Duyler\EventBus\Dto\Action as ActionDto;

class Action
{
    private static BusBuilder $busBuilder;

    public function __construct(BusBuilder $busBuilder)
    {
        static::$busBuilder = $busBuilder;
    }

    public static function add(
        string $id,
        string | Closure $handler,
        array $required = [],
        array $bind = [],
        array $providers = [],
        string $argument = '',
        ?string $contract = null,
        string | Closure $rollback = '',
        bool $externalAccess = false,
        bool $repeatable = false,
        bool $continueIfFail = true,
        bool $private = false,
        array $sealed = [],
        bool $silent = false,
    ): void {
        self::$busBuilder->addAction(
            new ActionDto(
                id: $id,
                handler: $handler,
                required: $required,
                bind: $bind,
                providers: $providers,
                argument: $argument,
                contract: $contract,
                rollback: $rollback,
                externalAccess: $externalAccess,
                repeatable: $repeatable,
                continueIfFail: $continueIfFail,
                private: $private,
                sealed: $sealed,
                silent: $silent,
            )
        );
    }

    public static function do(
        string $id,
        string | Closure $handler,
        array $required = [],
        array $bind = [],
        array $providers = [],
        string $argument = '',
        ?string $contract = null,
        string | Closure $rollback = '',
        bool $externalAccess = false,
        bool $repeatable = false,
        bool $continueIfFail = true,
        bool $private = false,
        array $sealed = [],
        bool $silent = false,
    ): void {
        self::$busBuilder->doAction(
            new ActionDto(
                id: $id,
                handler: $handler,
                required: $required,
                bind: $bind,
                providers: $providers,
                argument: $argument,
                contract: $contract,
                rollback: $rollback,
                externalAccess: $externalAccess,
                repeatable: $repeatable,
                continueIfFail: $continueIfFail,
                private: $private,
                sealed: $sealed,
                silent: $silent,
            )
        );
    }
}
