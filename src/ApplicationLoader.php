<?php

declare(strict_types=1);

namespace Duyler\Framework;

use Duyler\Framework\Loader\LoaderCollection;
use Duyler\Framework\Loader\ApplicationLoaderInterface;
use Override;

class ApplicationLoader implements ApplicationLoaderInterface
{
    #[Override]
    public function packages(LoaderCollection $loaderCollection): void {}
}
