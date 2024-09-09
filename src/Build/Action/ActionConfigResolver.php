<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\Action;

use Duyler\DependencyInjection\Definition;
use Duyler\DependencyInjection\Provider\ProviderInterface;

final class ActionConfigResolver
{
    public function resolve(array $config): ActionConfig
    {
        $actionProviders = [];
        $actionBind = [];
        $actionDefinitions = [];

        foreach ($config as $key => $value) {
            if (class_exists($key) || interface_exists($key)) {
                if (is_string($value)) {
                    $implements = class_implements($value);

                    if (is_array($implements) && in_array(ProviderInterface::class, $implements)) {
                        $actionProviders[$key] = $value;
                        continue;
                    }

                    if (interface_exists($key) && class_exists($value)) {
                        $actionBind[$key] = $value;
                        continue;
                    }
                }

                if (is_array($value)) {
                    $actionDefinitions[$key] = $value;
                    continue;
                }

                if (is_object($value) && is_a($value, Definition::class)) {
                    $actionDefinitions[$key] = $value->arguments;
                }
            }
        }

        return new ActionConfig($actionBind, $actionProviders, $actionDefinitions);
    }
}
