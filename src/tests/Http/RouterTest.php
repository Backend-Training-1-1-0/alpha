<?php

namespace Alpha\tests\Http;

use Alpha\Components\DIContainer\DIContainer;
use Alpha\Http\Router;
use Alpha\tests\Components\TestClass;
use Alpha\tests\Components\TestInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionProperty;

class RouterTest extends TestCase
{
    private array $config = [
        TestInterface::class => TestClass::class,
    ];

    /**
     * @covers Router::dispatch
     */
    public function testDispatch()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getServerParams')
            ->willReturn(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/']);
        $response = $this->createMock(ResponseInterface::class);
        $router = new Router(DIContainer::getInstance($this->config));

        $router->add('GET', '/', function () use ($response) {
            return $response;
        });

        $result = $router->dispatch($request);

        $this->assertSame($response, $result);
    }

    /**
     * @covers Router::addMiddleware
     */
    public function testAddMiddleware()
    {
        $router = new Router(DIContainer::getInstance($this->config));
        $middleware = function () {};
        $router->addMiddleware($middleware);

        $middlewaresProperty = new ReflectionProperty(Router::class, 'middlewares');
        $middlewaresProperty->setAccessible(true);
        $middlewares = $middlewaresProperty->getValue($router);
        $this->assertContains($middleware, $middlewares);
    }

    /**
     * @covers Router::add
     */
    public function testAdd()
    {
        $router = new Router(DIContainer::getInstance($this->config));

        $handler = function () {};
        $middleware = function () {};

        $router->add('GET', '/path', $handler, $middleware);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers Router::group
     */
    public function testGroup()
    {
        $router = new Router(DIContainer::getInstance($this->config));

        $groupCallback = function ($router) {
            $router->add('GET', '/path', function () {});
        };

        $middleware = function () {};

        $router->group('/prefix', $groupCallback, $middleware);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers Router::get
     */
    public function testGet()
    {
        $router = new Router(DIContainer::getInstance($this->config));

        $handler = function () {};
        $middleware = function () {};

        $router->get('/path', $handler, $middleware);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers Router::post
     */
    public function testPost()
    {
        $router = new Router(DIContainer::getInstance($this->config));

        $handler = function () {};
        $middleware = function () {};

        $router->post('/path', $handler, $middleware);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers Router::delete
     */
    public function testDelete()
    {
        $router = new Router(DIContainer::getInstance($this->config));

        $handler = function () {};
        $middleware = function () {};

        $router->delete('/path', $handler, $middleware);

        $this->expectNotToPerformAssertions();
    }
}