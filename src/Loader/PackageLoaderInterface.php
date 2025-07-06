<?php

declare(strict_types=1);

namespace Duyler\Builder\Loader;

interface PackageLoaderInterface
{
    public function beforeLoadBuild(LoaderServiceInterface $loaderService): void;

    public function afterLoadBuild(LoaderServiceInterface $loaderService): void;
}
