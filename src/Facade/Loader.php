<?php

declare(strict_types=1);

namespace Duyler\Framework\Facade;

use Duyler\Framework\Loader\LoaderCollection;

class Loader
{
    private static LoaderCollection $loaderCollection;

    public function __construct(LoaderCollection $loaderCollection)
    {
        static::$loaderCollection = $loaderCollection;
    }

    public static function add(string $loaderClass): void
    {
        static::$loaderCollection->add($loaderClass);
    }
}
