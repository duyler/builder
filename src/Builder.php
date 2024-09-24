<?php

declare(strict_types=1);

namespace Duyler\Builder;

use Dotenv\Dotenv;
use Duyler\Builder\Build\Action\Action;
use Duyler\Builder\Build\Action\ActionBuilder;
use Duyler\Builder\Build\AttributeHandlerCollection;
use Duyler\Builder\Build\BuilderCollection;
use Duyler\Builder\Build\Event\Event;
use Duyler\Builder\Build\Service\Service;
use Duyler\Builder\Build\State\StateContext;
use Duyler\Builder\Build\State\StateHandler;
use Duyler\Builder\Build\Trigger\Trigger;
use Duyler\Builder\Loader\ApplicationLoaderInterface;
use Duyler\Builder\Loader\LoaderCollection;
use Duyler\Builder\Loader\LoaderService;
use Duyler\Config\ConfigInterface;
use Duyler\Config\FileConfig;
use Duyler\DependencyInjection\Container;
use Duyler\DependencyInjection\ContainerConfig;
use Duyler\DependencyInjection\ContainerInterface as DuylerContainerInterface;
use Duyler\EventBus\Build\Context;
use Duyler\EventBus\Build\SharedService;
use Duyler\EventBus\BusBuilder;
use Duyler\EventBus\BusConfig;
use Duyler\EventBus\BusInterface;
use Duyler\EventBus\Contract\State\StateHandlerInterface;
use FilesystemIterator;
use LogicException;
use Psr\Container\ContainerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class Builder
{
    private BusBuilder $busBuilder;
    private FileConfig $config;
    private ContainerInterface $container;
    private AttributeHandlerCollection $attributeHandlerCollection;
    private BuilderCollection $builderCollection;
    private string $projectRootDir;

    public function __construct(private BuilderConfig $builderConfig = new BuilderConfig())
    {
        $dir = dirname('__DIR__') . '/';

        while (!is_file($dir . '/composer.json')) {
            if (is_dir(realpath($dir))) {
                $dir = $dir . '../';
            }

            if (false === realpath($dir)) {
                throw new LogicException('Cannot auto-detect project dir');
            }
        }

        $this->projectRootDir = $dir;

        $env = Dotenv::createImmutable($this->projectRootDir);

        $containerConfig = new ContainerConfig();
        $containerConfig->withBind([
            ApplicationLoaderInterface::class => ApplicationLoader::class,
        ]);

        $configCollector = new ConfigCollector($containerConfig);

        $this->config = new FileConfig(
            configDir: $this->projectRootDir . $this->builderConfig->configPath,
            env: $env->safeLoad() + $_ENV + [ConfigInterface::PROJECT_ROOT => $this->projectRootDir],
            externalConfigCollector: $configCollector,
        );

        $this->container = new Container($containerConfig);
        $this->container->set($this->config);
        $this->container->set($this->container);
        $this->container->bind(
            [
                ConfigInterface::class => FileConfig::class,
                ContainerInterface::class => Container::class,
                DuylerContainerInterface::class => Container::class,
            ],
        );

        /** @var  $busConfig BusConfig */
        $busConfig = $this->container->get(BusConfig::class);

        $this->busBuilder = new BusBuilder(
            new BusConfig(
                bind: $containerConfig->getClassMap() + $busConfig->bind,
                providers: $containerConfig->getProviders() + $busConfig->providers,
                definitions: $containerConfig->getDefinitions() + $busConfig->definitions,
                allowSkipUnresolvedActions: $busConfig->allowSkipUnresolvedActions,
                autoreset: $busConfig->autoreset,
                allowCircularCall: $busConfig->allowCircularCall,
                logMaxSize: $busConfig->logMaxSize,
                mode: $this->builderConfig->overrideBusMode === null
                    ? $busConfig->mode
                    : $this->builderConfig->overrideBusMode,
            ),
        );

        $this->busBuilder->addSharedService(new SharedService(
            class: FileConfig::class,
            service: $this->config,
            bind: [ConfigInterface::class => FileConfig::class],
        ));


        $this->attributeHandlerCollection = new AttributeHandlerCollection();
        $this->builderCollection = new BuilderCollection();
    }

    /** @return Container */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function addSharedService(object $object, array $bind = [], array $providers = []): Builder
    {
        $this->busBuilder->addSharedService(
            new SharedService(
                class: $object::class,
                service: $object,
                bind: $bind,
                providers: $providers,
            ),
        );
        return $this;
    }

    public function addStateHandler(StateHandlerInterface $stateHandler): Builder
    {
        $this->busBuilder->addStateHandler($stateHandler);
        return $this;
    }

    public function addStateContext(Context $context): Builder
    {
        $this->busBuilder->addStateContext($context);

        return $this;
    }

    public function addEvent(\Duyler\EventBus\Build\Event $event): Builder
    {
        $this->events[$event->id] = $event;

        return $this;
    }

    public function addAction(\Duyler\EventBus\Build\Action $action): Builder
    {
        $this->busBuilder->addAction($action);
        return $this;
    }

    public function doAction(\Duyler\EventBus\Build\Action $action): Builder
    {
        $this->busBuilder->doAction($action);
        return $this;
    }

    public function addTrigger(\Duyler\EventBus\Build\Trigger $trigger): Builder
    {
        $this->busBuilder->addTrigger($trigger);
        return $this;
    }

    public function build(): BusInterface
    {
        return $this->busBuilder->build();
    }

    public function loadBuild(): Builder
    {
        $actionBuilder = new ActionBuilder(
            $this->busBuilder,
        );

        new Trigger($this->busBuilder);
        new Action($actionBuilder);
        new Service($this->busBuilder, $this->container);
        new StateHandler($this->busBuilder, $this->container);
        new StateContext($this->busBuilder);
        new Event($this->busBuilder);

        $builder = new class {
            public function collect(string $path, ConfigInterface $config): void
            {
                require_once $path;
            }
        };

        $iterators = [];

        foreach ($this->builderConfig->buildPaths as $buildPath) {
            $iterators[] = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->projectRootDir . $buildPath, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD,
            );
        }

        foreach ($iterators as $iterator) {
            foreach ($iterator as $path => $dir) {
                if ($dir->isFile()) {
                    if ('php' === strtolower($dir->getExtension())) {
                        $builder->collect($path, $this->config);
                    }
                }
            }
        }

        $actionBuilder->build($this->attributeHandlerCollection);

        foreach ($this->builderCollection->getBuilders() as $builder) {
            $builder->build($this->attributeHandlerCollection);
        }

        return $this;
    }

    public function loadPackages(): Builder
    {
        $loaderCollection = new LoaderCollection();

        /** @var ApplicationLoaderInterface $loader */
        $loader = $this->container->get(ApplicationLoaderInterface::class);
        $loader->packages($loaderCollection);

        $packageLoaders = $loaderCollection->get();

        $loaderService = new LoaderService(
            $this->busBuilder,
            $this->attributeHandlerCollection,
            $this->builderCollection,
        );

        foreach ($packageLoaders as $loaderClass) {
            $packageLoader = $this->container->get($loaderClass);
            $packageLoader->load($loaderService);
        }

        return $this;
    }
}
