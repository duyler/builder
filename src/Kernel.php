<?php

declare(strict_types=1);

namespace Duyler\Framework;

use Dotenv\Dotenv;
use Duyler\Config\Config;
use Duyler\Config\ConfigFactory;
use Duyler\Contract\PackageLoader\PackageLoaderInterface;
use Duyler\DependencyInjection\ContainerBuilder;
use Duyler\DependencyInjection\ContainerInterface;
use Duyler\EventBus\BusBuilder;
use Duyler\Framework\Loader\LoaderCollection;
use Duyler\Framework\Facade\Action;
use Duyler\Framework\Facade\State;
use Duyler\Framework\Facade\Subscription;
use Duyler\Framework\Loader\LoaderService;
use FilesystemIterator;
use LogicException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

final class Kernel
{
    private BusBuilder $busBuilder;
    private Config $config;
    private ContainerInterface $container;
    private string $projectRootDir;

    public function __construct()
    {
        $dir = dirname('__DIR__') . '/';

        while (!is_file($dir . '/composer.json')) {
            if (is_dir(realpath($dir))) {
                $dir = $dir . '../';
            }

            if (realpath($dir) === false) {
                throw new LogicException('Cannot auto-detect project dir');
            }
        };

        $this->projectRootDir = $dir;

        $env = Dotenv::createImmutable($this->projectRootDir);

        $configFactory = new ConfigFactory();
        $this->config = $configFactory->create(
            $this->projectRootDir . 'config/',
            $env->safeLoad() + $_ENV + [Config::PROJECT_ROOT => $this->projectRootDir]
        );

        $containerConfig = new \Duyler\DependencyInjection\Config($this->getCacheDir());

        $this->container = ContainerBuilder::build($containerConfig);
        $this->container->set($this->config);
    }

    public function run(): void
    {
        $this->busBuilder = new BusBuilder();

        $this->build();

        $this->busBuilder
            ->setConfig(new \Duyler\EventBus\Dto\Config(
                defaultCacheDir: $this->getCacheDir()
            ))
            ->addSharedService($this->config)
            ->build()
            ->run();
    }

    private function build(): void
    {
        $loaderCollection = new LoaderCollection();

        new State($loaderCollection);
        new Subscription($this->busBuilder);
        new Action($this->busBuilder);

        $buildPath = $this->projectRootDir . 'build';

        $preload = $buildPath . DIRECTORY_SEPARATOR . 'preload.php';

        if (is_file($preload) ===false) {
            throw new RuntimeException(sprintf('File %s not found', $preload));
        }

        require $preload;

        $this->loadPackages($loaderCollection);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($buildPath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        foreach ($iterator as $path => $dir) {
            if ($dir->isFile() && $path !== $preload) {
                if (strtolower($dir->getExtension()) === 'php') {
                    include $path;
                }
            }
        }
    }

    private function loadPackages(LoaderCollection $preloader): void
    {
        $stateLoaders = $preloader->get();

        $loaderService = new LoaderService($this->container, $this->busBuilder);

        foreach ($stateLoaders as $loaderClass) {
            /** @var PackageLoaderInterface $loader */
            $loader = $this->container->make($loaderClass);
            $loader->load($loaderService);
        }
    }

    private function getCacheDir(): string
    {
        return $this->projectRootDir . '/var/cache/';
    }
}
