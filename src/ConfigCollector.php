<?php

declare(strict_types=1);

namespace Duyler\Builder;

use Duyler\Config\ConfigCollectorInterface;
use Duyler\DI\ContainerConfig;
use Duyler\DI\Definition;
use Duyler\DI\Provider\ProviderInterface;
use Override;

readonly class ConfigCollector implements ConfigCollectorInterface
{
    public function __construct(private ContainerConfig $containerConfig) {}

    #[Override]
    public function collect(string $key, mixed $value): void
    {
        if (class_exists($key) || interface_exists($key)) {
            if (is_string($value)) {
                $implements = class_implements($value);

                if ($implements && in_array(ProviderInterface::class, $implements)) {
                    $this->containerConfig->withProvider([$key => $value]);
                    return;
                }

                if (interface_exists($key) && class_exists($value)) {
                    $this->containerConfig->withBind([$key => $value]);
                    return;
                }
            }

            if (is_array($value)) {
                $this->containerConfig->withDefinition(new Definition(id: $key, arguments: $value));
                return;
            }

            if (is_object($value) && is_a($value, Definition::class)) {
                $this->containerConfig->withDefinition($value);
                return;
            }
        }
    }
}
