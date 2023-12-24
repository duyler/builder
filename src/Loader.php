<?php

declare(strict_types=1);

namespace Duyler\Framework;

use Duyler\Framework\Loader\LoaderCollection;
use Duyler\Framework\Loader\LoaderInterface;
use Override;

class Loader implements LoaderInterface
{
    #[Override]
    public function load(LoaderCollection $loaderCollection): void {}
}
