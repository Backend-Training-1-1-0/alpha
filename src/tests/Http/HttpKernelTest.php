<?php

namespace Alpha\tests\Http;

use Alpha\Contracts\HttpRouterInterface;
use Alpha\Http\HttpKernel;
use Alpha\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpKernelTest extends TestCase
{
    /**
     * @covers HttpKernel::handle
     */
    public function testHandle()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $router = $this->createMock(HttpRouterInterface::class);

        $kernel = new HttpKernel($request, $response, $router);

        $router->expects($this->once())
            ->method('dispatch')
            ->with($request)
            ->willReturn($response);

        $result = $kernel->handle();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * @covers HttpKernel::handle
     */
    public function testHandleWithJsonResponse()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $router = $this->createMock(HttpRouterInterface::class);
        $jsonResponse = $this->createMock(JsonResponse::class);

        $kernel = new HttpKernel($request, $response, $router);

        $router->expects($this->once())
            ->method('dispatch')
            ->with($request)
            ->willReturn($jsonResponse);

        $jsonResponse->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();

        $result = $kernel->handle();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($jsonResponse, $result);
    }
}