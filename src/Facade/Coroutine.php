<?php

declare(strict_types=1);

namespace Duyler\Framework\Facade;

use Closure;
use Duyler\EventBusCoroutine\Collector;

class Coroutine
{
    private static Collector $collector;

    public function __construct(Collector $collector)
    {
        static::$collector = $collector;
    }

    public static function add(
        string           $actionId,
        string | Closure $handler,
        string | Closure $callback,
        array            $classMap = [],
        array            $providers = [],
        string           $driver = 'pcntl',
    ): void {
        self::$collector->addCoroutine(
            new \Duyler\EventBusCoroutine\Dto\Coroutine(
                actionId: $actionId,
                callback: $callback,
                handler: $handler,
                classMap: $classMap,
                providers: $providers,
                driver: $driver,
            )
        );
    }
}
