<?php

declare(strict_types=1);

namespace Duyler\Framework\Facade;

use Duyler\DependencyInjection\ContainerInterface;
use Duyler\EventBus\BusBuilder;

final class Service
{
    private static BusBuilder $busBuilder;
    private static ContainerInterface $container;

    public function __construct(BusBuilder $busBuilder, ContainerInterface $container)
    {
        static::$busBuilder = $busBuilder;
        static::$container = $container;
    }

    public static function add(string $id, array $providers = [], array $bind = []): void
    {
        static::$container->setProviders($providers);
        static::$container->bind($bind);

        static::$busBuilder->addSharedService(static::$container->make($id));
    }
}
