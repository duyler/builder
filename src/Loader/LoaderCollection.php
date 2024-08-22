<?php

declare(strict_types=1);

namespace Duyler\Builder\Loader;

class LoaderCollection
{
    private array $loaders = [];

    public function add(string $loaderClass): void
    {
        $this->loaders[$loaderClass] = $loaderClass;
    }

    public function get(): array
    {
        return $this->loaders;
    }
}
