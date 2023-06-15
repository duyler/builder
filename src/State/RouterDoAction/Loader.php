<?php

declare(strict_types=1);

namespace Duyler\Framework\State\RouterDoAction;

use Duyler\Contract\PackageLoader\LoaderServiceInterface;
use Duyler\Contract\PackageLoader\PackageLoaderInterface;

class Loader implements PackageLoaderInterface
{
    public function load(LoaderServiceInterface $loaderService): void
    {
        $stateHandler = $loaderService->getContainer()->make(RouterDoActionStateHandler::class);
        $loaderService->getBuilder()->addStateHandler($stateHandler);
    }
}
