<?php

declare(strict_types=1);

namespace Duyler\Framework\Loader;

interface LoaderInterface
{
    public function packages(LoaderCollection $loaderCollection): void;
    public function runners(): array;
}
