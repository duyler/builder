<?php

declare(strict_types=1);

namespace Duyler\Framework;

use Dotenv\Dotenv;
use Duyler\ActionBus\Build\SharedService;
use Duyler\Config\ConfigInterface;
use Duyler\Config\FileConfig;
use Duyler\DependencyInjection\Container;
use Duyler\DependencyInjection\ContainerInterface as DuylerContainerInterface;
use Duyler\DependencyInjection\ContainerConfig;
use Duyler\ActionBus\BusBuilder;
use Duyler\ActionBus\BusConfig;
use Duyler\ActionBus\BusInterface;
use Duyler\Framework\Build\Action\Action;
use Duyler\Framework\Build\Action\ActionBuilder;
use Duyler\Framework\Build\AttributeHandlerCollection;
use Duyler\Framework\Build\BuilderCollection;
use Duyler\Framework\Build\Event\Event;
use Duyler\Framework\Build\Service\Service;
use Duyler\Framework\Build\State\StateContext;
use Duyler\Framework\Build\State\StateHandler;
use Duyler\Framework\Build\Subscription\Subscription;
use Duyler\Framework\Loader\LoaderCollection;
use Duyler\Framework\Loader\ApplicationLoaderInterface;
use Duyler\Framework\Loader\LoaderService;
use FilesystemIterator;
use LogicException;
use Psr\Container\ContainerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Builder
{
    private BusBuilder $busBuilder;
    private FileConfig $config;
    private ContainerInterface $container;
    private AttributeHandlerCollection $attributeHandlerCollection;
    private BuilderCollection $builderCollection;
    private string $projectRootDir;

    public function __construct(string $configPath = 'config')
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
            configDir: $this->projectRootDir . $configPath,
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
                mode: $busConfig->mode,
                resetMode: $busConfig->resetMode,
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

    public function addSharedService(object $object, array $bind = [], array $providers = []): void
    {
        $this->busBuilder->addSharedService(
            new SharedService(
                class: $object::class,
                service: $object,
                bind: $bind,
                providers: $providers,
            ),
        );
    }

    public function build(): BusInterface
    {
        return $this->busBuilder->build();
    }

    public function loadBuild(): void
    {
        $actionBuilder = new ActionBuilder(
            $this->busBuilder,
        );

        new Subscription($this->busBuilder);
        new Action($actionBuilder);
        new Service($this->busBuilder, $this->container);
        new StateHandler($this->busBuilder, $this->container);
        new StateContext($this->busBuilder);
        new Event($this->busBuilder);

        $builder = new class () {
            public function collect(string $path, ConfigInterface $config): void
            {
                require_once $path;
            }
        };

        /** @var  $builderConfig BuilderConfig */
        $builderConfig = $this->container->get(BuilderConfig::class);
        $buildPath = $this->projectRootDir . $builderConfig->buildPath;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($buildPath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD,
        );

        foreach ($iterator as $path => $dir) {
            if ($dir->isFile()) {
                if ('php' === strtolower($dir->getExtension())) {
                    $builder->collect($path, $this->config);
                }
            }
        }

        $actionBuilder->build($this->attributeHandlerCollection);

        foreach ($this->builderCollection->getBuilders() as $builder) {
            $builder->build($this->attributeHandlerCollection);
        }
    }

    public function loadPackages(): void
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
    }
}
