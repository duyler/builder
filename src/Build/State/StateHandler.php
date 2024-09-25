<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\State;

use Duyler\DI\ContainerInterface;
use Duyler\EventBus\BusBuilder;

class StateHandler
{
    private static BusBuilder $busBuilder;
    private static ContainerInterface $container;

    public function __construct(BusBuilder $busBuilder, ContainerInterface $container)
    {
        self::$busBuilder = $busBuilder;
        self::$container = $container;
    }

    public static function add(string $class, array $providers = [], array $bind = []): void
    {
        self::$container->addProviders($providers);
        self::$container->bind($bind);

        self::$busBuilder->addStateHandler(self::$container->get($class));
    }
}
