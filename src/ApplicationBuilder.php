<?php

declare(strict_types=1);

namespace Duyler\Builder;

use Duyler\Builder\Config\BusConfig;
use Duyler\Builder\Loader\ApplicationLoaderInterface;
use Duyler\Config\ConfigInterface;
use Duyler\Config\FileConfig;
use Duyler\DI\Container;
use Duyler\DI\ContainerConfig;
use Duyler\DI\ContainerInterface as DuylerContainerInterface;
use Duyler\EventBus\Build\SharedService;
use Duyler\EventBus\BusBuilder as EventBusBuilder;
use Duyler\EventBus\BusConfig as EventBusConfig;
use Psr\Container\ContainerInterface;

final class ApplicationBuilder
{
    private ?EventBusBuilder $busBuilder = null;
    private FileConfig $config;
    private ContainerConfig $containerConfig;
    private ContainerInterface $container;

    public function __construct(string $configDir = 'config', string $rootFile = 'docker-compose.yml')
    {
        $this->containerConfig = new ContainerConfig();
        $this->containerConfig->withBind([
            ApplicationLoaderInterface::class => ApplicationLoader::class,
        ]);

        $configCollector = new ConfigCollector($this->containerConfig);

        $this->config = new FileConfig(
            configDir: $configDir,
            rootFile: $rootFile,
            externalConfigCollector: $configCollector,
        );

        $this->container = new Container($this->containerConfig);
        $this->container->set($this->config);
        $this->container->set($this->container);
        $this->container->bind(
            [
                ConfigInterface::class => FileConfig::class,
                ContainerInterface::class => Container::class,
                DuylerContainerInterface::class => Container::class,
            ],
        );
    }

    public function getBusBuilder(?BusConfig $eventBusConfig = null): BusBuilder
    {
        /** @var $eventBusConfig EventBusConfig */
        $eventBusConfig = $eventBusConfig ?? $this->container->get(EventBusConfig::class);

        $this->busBuilder = new EventBusBuilder(
            new EventBusConfig(
                bind: $this->containerConfig->getClassMap() + $eventBusConfig->bind,
                providers: $this->containerConfig->getProviders() + $eventBusConfig->providers,
                definitions: $this->containerConfig->getDefinitions() + $eventBusConfig->definitions,
                allowSkipUnresolvedActions: $eventBusConfig->allowSkipUnresolvedActions,
                autoreset: $eventBusConfig->autoreset,
                allowCircularCall: $eventBusConfig->allowCircularCall,
                logMaxSize: $eventBusConfig->logMaxSize,
                mode: $eventBusConfig->mode,
            ),
        );

        $this->busBuilder->addSharedService(new SharedService(
            class: FileConfig::class,
            service: $this->config,
            bind: [ConfigInterface::class => FileConfig::class],
        ));

        return new BusBuilder($this->busBuilder, $this->container);
    }

    /** @return Container */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
