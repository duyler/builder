<?php

declare(strict_types=1);

namespace Duyler\Framework;

use Dotenv\Dotenv;
use Duyler\Config\FileConfig;
use Duyler\DependencyInjection\Container;
use Duyler\DependencyInjection\ContainerConfig;
use Duyler\DependencyInjection\Exception\InterfaceMapNotFoundException;
use Duyler\EventBus\BusBuilder;
use Duyler\EventBus\BusInterface;
use Duyler\EventBus\Dto\Config;
use Duyler\Framework\Facade\Action;
use Duyler\Framework\Facade\Service;
use Duyler\Framework\Facade\Subscription;
use Duyler\Framework\Http\RequestProvider;
use Duyler\Framework\Http\ResponseEmitter;
use Duyler\Framework\Loader\LoaderCollection;
use Duyler\Framework\Loader\LoaderInterface;
use Duyler\Framework\Loader\LoaderService;
use FilesystemIterator;
use HttpSoft\Response\EmptyResponse;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

final class Application
{
    private BusBuilder $busBuilder;
    private FileConfig $config;
    private ContainerInterface $container;
    private ResponseEmitter $responseEmitter;
    private RequestProvider $requestProvider;
    private BusInterface $bus;
    private string $projectRootDir;

    public function __construct()
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
            LoaderInterface::class => Loader::class
        ]);

        $configCollector = new ConfigCollector($containerConfig);

        $this->config = new FileConfig(
            configDir: $this->projectRootDir . 'config',
            env: $env->safeLoad() + $_ENV + [FileConfig::PROJECT_ROOT => $this->projectRootDir],
            externalConfigCollector: $configCollector,
        );

        $this->requestProvider = new RequestProvider();
        $this->responseEmitter = new ResponseEmitter(
            new EmptyResponse(404),
        );

        $this->container = new Container($containerConfig);
        $this->container->set($this->config);
        $this->container->set($this->responseEmitter);
        $this->container->set($this->requestProvider);

        $this->busBuilder = new BusBuilder(
            new Config(
                bind: $containerConfig->getClassMap(),
                providers: $containerConfig->getProviders(),
                definitions: $containerConfig->getDefinitions(),
            )
        );

        $this->busBuilder->addSharedService($this->config);
        $this->busBuilder->addSharedService($this->requestProvider);
        $this->busBuilder->addSharedService($this->responseEmitter);

        $this->loadPackages();
        $this->build();

        $this->bus = $this->busBuilder->build();
    }

    /**
     * @throws InterfaceMapNotFoundException
     * @throws Throwable
     */
    public function run(?ServerRequestInterface $request = null): ResponseInterface
    {
        if ($request !== null) {
            $this->container->set($request);
            $this->container->bind([ServerRequestInterface::class => $request::class]);
            $this->requestProvider->set($request);
        }

        $this->bus->run();
        return $this->responseEmitter->getResponse();
    }

    private function build(): void
    {
        new Subscription($this->busBuilder);
        new Action($this->busBuilder);
        new Service($this->busBuilder, $this->container);

        $builder = new class () {
            public function collect(string $path): void
            {
                require_once $path;
            }
        };

        $buildPath = $this->projectRootDir . 'build';

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($buildPath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        foreach ($iterator as $path => $dir) {
            if ($dir->isFile()) {
                if ('php' === strtolower($dir->getExtension())) {
                    $builder->collect($path);
                }
            }
        }
    }

    private function loadPackages(): void
    {
        $loaderCollection = new LoaderCollection();

        /** @var LoaderInterface $loader */
        $loader = $this->container->get(LoaderInterface::class);
        $loader->load($loaderCollection);

        $packageLoaders = $loaderCollection->get();

        $loaderService = new LoaderService($this->container, $this->busBuilder, $this->config);

        foreach ($packageLoaders as $loaderClass) {
            $packageLoader = $this->container->get($loaderClass);
            $packageLoader->load($loaderService);
        }
    }
}
