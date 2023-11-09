<?php

namespace Alpha\tests\Components;

use Alpha\Components\DIContainer\DIContainer;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{
    private array $config = [
        TestInterface::class => TestClass::class,
    ];

    /**
     * @covers DIContainer::getInstance
     */
    public function testGetInstance(): void
    {
        $instance1 = DIContainer::getInstance($this->config);
        $instance2 = DIContainer::getInstance($this->config);

        $this->assertInstanceOf(DIContainer::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    /**
     * @covers DIContainer::build
     */
    public function testBuild(): void
    {
        $container = DIContainer::getInstance($this->config);
        $object = $container->build(TestClass::class);

        $this->assertInstanceOf(TestClass::class, $object);
    }

    /**
     * @covers DIContainer::make
     */
    public function testMake(): void
    {
        $container = DIContainer::getInstance($this->config);
        $object = $container->make(TestInterface::class);

        $this->assertInstanceOf(TestClass::class, $object);
    }

    /**
     * @covers DIContainer::singleton
     */
    public function testSingleton(): void
    {
        $container = DIContainer::getInstance($this->config);
        $container->singleton(TestInterface::class, TestClass::class);
        $object1 = $container->get(TestInterface::class);
        $object2 = $container->get(TestInterface::class);

        $this->assertInstanceOf(TestClass::class, $object1);
        $this->assertSame($object1, $object2);
    }

    /**
     * @covers DIContainer::call
     */
    public function testCall(): void
    {
        $container = DIContainer::getInstance($this->config);
        $result = $container->call(function (int $a, int $b) {
            return $a + $b;
        }, null, [1, 2]);

        $this->assertEquals(3, $result);
    }

    /**
     * @covers DIContainer::get
     */
    public function testGet(): void
    {
        $container = DIContainer::getInstance($this->config);
        $container->singleton(TestInterface::class, TestClass::class);
        $object = $container->get(TestInterface::class);

        $this->assertInstanceOf(TestClass::class, $object);
    }
}
