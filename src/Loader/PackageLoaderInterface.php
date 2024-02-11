<?php

declare(strict_types=1);

namespace Duyler\Framework\Loader;

interface PackageLoaderInterface
{
    public function load(LoaderServiceInterface $loaderService): void;
}
