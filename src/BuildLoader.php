<?php

declare(strict_types=1);

namespace Duyler\Builder;

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
use Duyler\Config\ConfigInterface;
use Duyler\DI\ContainerInterface;
use Duyler\EventBus\BusBuilder as EventBusBuilder;
use Duyler\EventBus\BusInterface;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class BuildLoader
{
    public function __construct(
        private EventBusBuilder $busBuilder,
        private ContainerInterface $container,
    ) {}

    public function loadBuild(?BuildConfig $builderConfig = null): BuildLoader
    {
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

        /** @var ConfigInterface $config */
        $config = $this->container->get(ConfigInterface::class);

        foreach ($builderConfig->buildPaths as $buildPath) {
            $iterators[] = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($config->path($buildPath), FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD,
            );
        }

        foreach ($iterators as $iterator) {
            foreach ($iterator as $path => $dir) {
                if ($dir->isFile()) {
                    if ('php' === strtolower($dir->getExtension())) {
                        $builder->collect($path, $config);
                    }
                }
            }
        }

        /** @var AttributeHandlerCollection $attributeHandlerCollection */
        $attributeHandlerCollection = $this->container->get(AttributeHandlerCollection::class);

        /** @var BuilderCollection $builderCollection */
        $builderCollection = $this->container->get(BuilderCollection::class);

        $actionBuilder->build($attributeHandlerCollection);

        foreach ($builderCollection->getBuilders() as $builder) {
            $builder->build($attributeHandlerCollection);
        }

        return $this;
    }

    public function build(): BusInterface
    {
        return $this->busBuilder->build();
    }
}
