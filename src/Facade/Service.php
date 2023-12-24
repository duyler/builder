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
        self::$busBuilder = $busBuilder;
        self::$container = $container;
    }

    public static function add(string $id, array $providers = [], array $bind = []): void
    {
        self::$container->addProviders($providers);
        self::$container->bind($bind);

        self::$busBuilder->addSharedService(self::$container->get($id));
    }
}
