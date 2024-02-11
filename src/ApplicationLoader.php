<?php

declare(strict_types=1);

namespace Duyler\Framework;

use Duyler\Framework\Loader\LoaderCollection;
use Duyler\Framework\Loader\ApplicationLoaderInterface;
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
