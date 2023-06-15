<?php

declare(strict_types=1);

namespace Duyler\Framework\Service\Router\Action;

use Duyler\Config\Config;
use Duyler\Router\Result;
use Duyler\Router\Router;
use Symfony\Component\HttpFoundation\Request;

readonly class StartRoutingAction
{
    public function __construct(private Router $router, private Request $request, private Config $config)
    {
    }

    public function __invoke(): Result
    {
        $this->router->setRoutesDirPath(
            $this->config->env(Config::PROJECT_ROOT)
            . $this->config->get('router', 'routes_dir')
            . DIRECTORY_SEPARATOR
        );

        $result = $this->router->startRouting();

        if ($result->status) {
            foreach ($result->attributes as $key => $value) {
                $this->request->attributes->set($key, $value);
            }
        }

        return $result;
    }
}
