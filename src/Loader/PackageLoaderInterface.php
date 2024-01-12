<?php

declare(strict_types=1);

namespace Duyler\Framework\Loader;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;

interface PackageLoaderInterface
{
    public function load(LoaderServiceInterface $loaderService): void;
}
