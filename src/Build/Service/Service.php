<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\Service;

use Duyler\DI\ContainerInterface;
use Duyler\EventBus\Build\SharedService;
use Duyler\EventBus\BusBuilder;

final class Service
{
    private static BusBuilder $busBuilder;
    private static ContainerInterface $container;

    public function __construct(BusBuilder $busBuilder, ContainerInterface $container)
    {
        self::$busBuilder = $busBuilder;
        self::$container = $container;
    }

    public static function add(string $id, array $providers = [], array $bind = []): void
    {
        self::$container->addProviders($providers);
        self::$container->bind($bind);

        self::$busBuilder->addSharedService(new SharedService(
            class: $id,
            service: self::$container->get($id),
            bind: $bind,
            providers: $providers,
        ));
    }
}
