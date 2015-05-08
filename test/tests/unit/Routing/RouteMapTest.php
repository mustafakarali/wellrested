<?php

namespace WellRESTed\Test\Unit\Routing;

use Prophecy\Argument;
use WellRESTed\Routing\Route\RouteInterface;
use WellRESTed\Routing\RouteMap;

/**
 * @coversDefaultClass WellRESTed\Routing\RouteMap
 * @uses WellRESTed\Routing\RouteMap
 */
class RouteMapTest extends \PHPUnit_Framework_TestCase
{
    private $methodMap;
    private $factory;
    private $request;
    private $response;
    private $route;
    private $routeMap;

    public function setUp()
    {
        parent::setUp();

        $this->methodMap = $this->prophesize('WellRESTed\Routing\MethodMapInterface');
        $this->methodMap->setMethod(Argument::cetera());

        $this->route = $this->prophesize('WellRESTed\Routing\Route\RouteInterface');
        $this->route->dispatch(Argument::cetera())->willReturn();
        $this->route->getMethodMap()->willReturn($this->methodMap->reveal());
        $this->route->getType()->willReturn(RouteInterface::TYPE_STATIC);
        $this->route->getTarget()->willReturn("/");

        $this->factory = $this->prophesize('WellRESTed\Routing\Route\RouteFactory');
        $this->factory->create(Argument::any())->willReturn($this->route->reveal());

        $this->request = $this->prophesize('Psr\Http\Message\ServerRequestInterface');

        $this->response = $this->prophesize('Psr\Http\Message\ResponseInterface');

        $this->routeMap = $this->getMockBuilder('WellRESTed\Routing\RouteMap')
            ->setMethods(["getRouteFactory"])
            ->disableOriginalConstructor()
            ->getMock();
        $this->routeMap->expects($this->any())
            ->method("getRouteFactory")
            ->will($this->returnValue($this->factory->reveal()));
        $this->routeMap->__construct();
    }

    // ------------------------------------------------------------------------
    // Construction

    /**
     * @covers ::__construct
     * @covers ::getRouteFactory
     */
    public function testCreatesInstance()
    {
        $routeMap = new RouteMap();
        $this->assertNotNull($routeMap);
    }

    // ------------------------------------------------------------------------
    // Populating

    /**
     * @covers ::add
     * @covers ::getRouteForTarget
     * @covers ::registerRouteForTarget
     */
    public function testAddCreatesRouteForTarget()
    {
        $this->routeMap->add("GET", "/", "middleware");
        $this->factory->create("/")->shouldHaveBeenCalled();
    }

    /**
     * @covers ::add
     * @covers ::getRouteForTarget
     */
    public function testAddDoesNotRecreateRouteForExistingTarget()
    {
        $this->routeMap->add("GET", "/", "middleware");
        $this->routeMap->add("POST", "/", "middleware");
        $this->factory->create("/")->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @covers ::add
     */
    public function testAddPassesMethodAndMiddlewareToMethodMap()
    {
        $this->routeMap->add("GET", "/", "middleware");
        $this->methodMap->setMethod("GET", "middleware")->shouldHaveBeenCalled();
    }

    // ------------------------------------------------------------------------
    // Dispatching

    /**
     * @covers ::dispatch
     * @covers ::getStaticRoute
     * @covers ::registerRouteForTarget
     */
    public function testDispatchesStaticRoute()
    {
        $target = "/";

        $this->request->getRequestTarget()->willReturn($target);
        $this->route->getTarget()->willReturn($target);
        $this->route->getType()->willReturn(RouteInterface::TYPE_STATIC);

        $this->routeMap->add("GET", $target, "middleware");

        $request = $this->request->reveal();
        $response = $this->response->reveal();
        $this->routeMap->dispatch($request, $response);

        $this->route->dispatch(Argument::cetera())->shouldHaveBeenCalled();
    }

    /**
     * @covers ::dispatch
     * @covers ::getPrefixRoute
     * @covers ::registerRouteForTarget
     */
    public function testDispatchesPrefixRoute()
    {
        $target = "/*";

        $this->request->getRequestTarget()->willReturn($target);
        $this->route->getTarget()->willReturn($target);
        $this->route->getType()->willReturn(RouteInterface::TYPE_PREFIX);

        $this->routeMap->add("GET", $target, "middleware");

        $request = $this->request->reveal();
        $response = $this->response->reveal();
        $this->routeMap->dispatch($request, $response);

        $this->route->dispatch(Argument::cetera())->shouldHaveBeenCalled();
    }
}
