<?php

declare(strict_types=1);

namespace Duyler\Builder\Loader;

interface PackageLoaderInterface
{
    public function load(LoaderServiceInterface $loaderService): void;
}
