<?php

declare(strict_types=1);

namespace Duyler\Builder\Loader;

interface ApplicationLoaderInterface
{
    public function packages(LoaderCollection $loaderCollection): void;
}
