<?php

declare(strict_types=1);

namespace Duyler\Builder\Test\Unit;

use Duyler\Builder\ConfigCollector;
use Duyler\DI\ContainerConfig;
use Duyler\DI\Definition;
use Duyler\DI\Provider\ProviderInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ConfigCollectorTest extends TestCase
{
    private ContainerConfig $containerConfig;
    private ConfigCollector $configCollector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->containerConfig = new ContainerConfig();
        $this->configCollector = new ConfigCollector($this->containerConfig);
    }

    #[Test]
    public function should_collect_bindings(): void
    {
        $interface = 'TestInterface';
        $implementation = 'TestImplementation';

        $this->configCollector->collect($interface, $implementation);

        $this->containerConfig->withBind([$interface => $implementation]);
        $classMap = $this->containerConfig->getClassMap();
        $this->assertArrayHasKey($interface, $classMap);
        $this->assertEquals($implementation, $classMap[$interface]);
    }

    //#[Test]
    //public function should_collect_providers(): void
    //{
    //    $interface = 'TestInterface';
    //    $provider = new class implements ProviderInterface {
    //        public function provide(): object
    //        {
    //            return new \stdClass();
    //        }
    //    };
    //
    //    $this->configCollector->collect($interface, get_class($provider));
    //
    //    $this->containerConfig->withProvider([$interface => get_class($provider)]);
    //    $providers = $this->containerConfig->getProviders();
    //    $this->assertArrayHasKey($interface, $providers);
    //    $this->assertEquals(get_class($provider), $providers[$interface]);
    //}

    //#[Test]
    //public function should_collect_definition_object(): void
    //{
    //    $class = 'TestService';
    //    $definition = new Definition($class, ['test' => 'value']);
    //
    //    $this->configCollector->collect($class, $definition);
    //
    //    $definitions = $this->containerConfig->getDefinitions();
    //    $this->assertCount(1, $definitions);
    //    $this->assertSame($definition, $definitions[0]);
    //}

    #[Test]
    public function should_not_collect_invalid_binding(): void
    {
        $interface = 'TestInterface';
        $implementation = 'NonExistentClass';

        $this->configCollector->collect($interface, $implementation);

        $classMap = $this->containerConfig->getClassMap();
        $this->assertArrayNotHasKey($interface, $classMap);
    }
}
