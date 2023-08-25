<?php

declare(strict_types=1);

namespace Duyler\Framework\Facade;

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
        string         $id,
        Closure|string $handler,
        array          $required = [],
        array          $classMap = [],
        array          $providers = [],
        Closure|string $rollback = '',
        array          $arguments = [],
        bool           $externalAccess = false,
        ?string        $contract = null,
    ): void {
        self::$busBuilder->addAction(
            new ActionDto(
                id: $id,
                handler: $handler,
                required: $required,
                classMap: $classMap,
                providers: $providers,
                rollback: $rollback,
                arguments: $arguments,
                externalAccess: $externalAccess,
                contract: $contract,
            )
        );
    }

    public static function do(
        string         $id,
        Closure|string $handler,
        array          $required = [],
        array          $classMap = [],
        array          $providers = [],
        Closure|string $rollback = '',
        array          $arguments = [],
        bool           $externalAccess = false,
        ?string        $contract = null,
    ): void {
        self::$busBuilder->doAction(
            new ActionDto(
                id: $id,
                handler: $handler,
                required: $required,
                classMap: $classMap,
                providers: $providers,
                rollback: $rollback,
                arguments: $arguments,
                externalAccess: $externalAccess,
                contract: $contract,
            )
        );
    }
}
