<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Interop\Container\ContainerInterface;
use Laminas\Hydrator\DelegatingHydrator;
use Laminas\Hydrator\DelegatingHydratorFactory;
use Laminas\Hydrator\HydratorPluginManager;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @covers Laminas\Hydrator\DelegatingHydratorFactory
 */
class DelegatingHydratorFactoryTest extends TestCase
{
    public function testFactoryUsesContainerToSeedDelegatingHydratorWhenItIsAHydratorPluginManager()
    {
        $hydrators = $this->prophesize(HydratorPluginManager::class)->reveal();
        $factory = new DelegatingHydratorFactory();

        $hydrator = $factory($hydrators, '');
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);
    }

    public function testFactoryUsesHydratorPluginManagerServiceFromContainerToSeedDelegatingHydratorWhenAvailable()
    {
        $hydrators = $this->prophesize(HydratorPluginManager::class)->reveal();
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(HydratorPluginManager::class)->willReturn(true);
        $container->get(HydratorPluginManager::class)->willReturn($hydrators);

        $factory = new DelegatingHydratorFactory();

        $hydrator = $factory($container->reveal(), '');
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);
    }

    public function testFactoryUsesHydratorManagerServiceFromContainerToSeedDelegatingHydratorWhenAvailable()
    {
        $hydrators = $this->prophesize(HydratorPluginManager::class)->reveal();
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(HydratorPluginManager::class)->willReturn(false);
        $container->has(\Zend\Hydrator\HydratorPluginManager::class)->willReturn(false);
        $container->has('HydratorManager')->willReturn(true);
        $container->get('HydratorManager')->willReturn($hydrators);

        $factory = new DelegatingHydratorFactory();

        $hydrator = $factory($container->reveal(), '');
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);
    }

    public function testFactoryCreatesHydratorPluginManagerToSeedDelegatingHydratorAsFallback()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(HydratorPluginManager::class)->willReturn(false);
        $container->has(\Zend\Hydrator\HydratorPluginManager::class)->willReturn(false);
        $container->has('HydratorManager')->willReturn(false);

        $factory = new DelegatingHydratorFactory();

        $hydrator = $factory($container->reveal(), '');
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);

        $r = new ReflectionProperty($hydrator, 'hydrators');
        $r->setAccessible(true);
        $hydrators = $r->getValue($hydrator);

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
    }
}
