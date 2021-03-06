<?php

namespace WellRESTed\Test\Unit\Routing\Route;

use Prophecy\Argument;
use WellRESTed\Routing\Route\RouteFactory;
use WellRESTed\Routing\Route\RouteInterface;

/**
 * @covers WellRESTed\Routing\Route\RouteFactory
 * @group route
 * @group routing
 */
class RouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->prophesize('WellRESTed\Dispatching\DispatcherInterface');
    }

    public function testCreatesStaticRoute()
    {
        $factory = new RouteFactory($this->dispatcher->reveal());
        $route = $factory->create("/cats/");
        $this->assertSame(RouteInterface::TYPE_STATIC, $route->getType());
    }

    public function testCreatesPrefixRoute()
    {
        $factory = new RouteFactory($this->dispatcher->reveal());
        $route = $factory->create("/cats/*");
        $this->assertSame(RouteInterface::TYPE_PREFIX, $route->getType());
    }

    public function testCreatesRegexRoute()
    {
        $factory = new RouteFactory($this->dispatcher->reveal());
        $route = $factory->create("~/cat/[0-9]+~");
        $this->assertSame(RouteInterface::TYPE_PATTERN, $route->getType());
    }

    public function testCreatesTemplateRoute()
    {
        $factory = new RouteFactory($this->dispatcher->reveal());
        $route = $factory->create("/cat/{id}");
        $this->assertSame(RouteInterface::TYPE_PATTERN, $route->getType());
    }
}
