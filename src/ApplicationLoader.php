<?php

declare(strict_types=1);

namespace Duyler\Builder;

use Duyler\Builder\Loader\LoaderCollection;
use Duyler\Builder\Loader\ApplicationLoaderInterface;
use Override;

class ApplicationLoader implements ApplicationLoaderInterface
{
    /** @var string[] */
    private array $packages = [];

    /** @param string[] $packages */
    public function __construct(array $packages = [])
    {
        $this->packages = $this->packages + $packages;
    }

    #[Override]
    public function packages(LoaderCollection $loaderCollection): void
    {
        foreach ($this->packages as $package) {
            $loaderCollection->add($package);
        }
    }
}
