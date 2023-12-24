<?php

declare(strict_types=1);

namespace Duyler\Framework\Loader;

interface LoaderInterface
{
    public function load(LoaderCollection $loaderCollection): void;
}
