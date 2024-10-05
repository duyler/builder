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
use Duyler\Builder\Config\BuildConfig;
use Duyler\Builder\Config\BusConfig;
use Duyler\Builder\Config\PackagesConfig;
use Duyler\Builder\Loader\ApplicationLoaderInterface;
use Duyler\Builder\Loader\LoaderService;
use Duyler\Config\ConfigInterface;
use Duyler\Config\FileConfig;
use Duyler\DI\Container;
use Duyler\DI\ContainerConfig;
use Duyler\DI\ContainerInterface as DuylerContainerInterface;
use Duyler\EventBus\Build\SharedService;
use Duyler\EventBus\BusBuilder as EventBusBuilder;
use Duyler\EventBus\BusConfig as EventBusConfig;
use Duyler\EventBus\BusInterface;
use FilesystemIterator;
use LogicException;
use Psr\Container\ContainerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class ApplicationBuilder
{
    private ?EventBusBuilder $busBuilder = null;
    private FileConfig $config;
    private ContainerConfig $containerConfig;
    private ContainerInterface $container;
    private AttributeHandlerCollection $attributeHandlerCollection;
    private BuilderCollection $builderCollection;
    private string $projectRootDir;

    public function __construct(string $configDir = 'config')
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

        $this->containerConfig = new ContainerConfig();
        $this->containerConfig->withBind([
            ApplicationLoaderInterface::class => ApplicationLoader::class,
        ]);

        $env = Dotenv::createImmutable($this->projectRootDir);

        $configCollector = new ConfigCollector($this->containerConfig);

        $this->config = new FileConfig(
            configDir: $this->projectRootDir . $configDir,
            env: $env->safeLoad() + $_ENV,
            root: $this->projectRootDir,
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

        $this->attributeHandlerCollection = new AttributeHandlerCollection();
        $this->builderCollection = new BuilderCollection();
    }

    public function loadBus(?BusConfig $eventBusConfig = null): BusBuilder
    {
        /** @var  $eventBusConfig EventBusConfig */
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

        return new BusBuilder($this->busBuilder);
    }

    public function loadPackages(?PackagesConfig $packagesConfig = null): ApplicationBuilder
    {
        $this->checkCondition();

        /** @var PackagesConfig $packagesConfig */
        $packagesConfig = $packagesConfig ?? $this->container->get(PackagesConfig::class);

        $loaderService = new LoaderService(
            $this->busBuilder,
            $this->attributeHandlerCollection,
            $this->builderCollection,
        );

        foreach ($packagesConfig->packages as $loaderClass) {
            $packageLoader = $this->container->get($loaderClass);
            $packageLoader->load($loaderService);
        }

        return $this;
    }

    public function loadBuild(?BuildConfig $builderConfig = null): ApplicationBuilder
    {
        $this->checkCondition();

        $builderConfig = $builderConfig ?? $this->container->get(BuildConfig::class);

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

        foreach ($builderConfig->buildPaths as $buildPath) {
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

    public function build(): BusInterface
    {
        $this->checkCondition();
        return $this->busBuilder->build();
    }

    /** @return Container */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    private function checkCondition(): void
    {
        $this->busBuilder ?? throw new LogicException('Bus builder not load');
    }
}
