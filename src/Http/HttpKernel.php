<?php

namespace Alpha\Http;

use Alpha\Contracts\{
    HttpKernelInterface,
    HttpRouterInterface,
};
use RuntimeException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpKernel implements HttpKernelInterface
{
    public function __construct(
        private ServerRequestInterface $request,
        private ResponseInterface $response,
        private HttpRouterInterface $router,
    ) {}

    public function handle(): ResponseInterface
    {
        $this->setErrorHandler(function (\Throwable $exception) {
            $body = Stream::create("Код ошибки: {$exception->getCode()} <br> Сообщение: {$exception->getMessage()}");

            $this->response->withBody($body)->send();
        });

        $response = $this->router->dispatch($this->request);

        if ($response instanceof JsonResponse) {
            return $response->withHeader('Content-Type','application/json');
        }

        if ($response instanceof ResponseInterface) {
            return $response;
        }
        
        return $this->response->withBody(Stream::create($response));
    }

    private function setErrorHandler(callable $handlerCallback)
    {
        set_exception_handler(function ($exception) use ($handlerCallback) {
            $handlerCallback($exception);
        });

        set_error_handler(function ($errorLevel, $errorMessage, $errorFile, $errorLine) use ($handlerCallback) {
            $handlerCallback(new RuntimeException("Runtime error [$errorLevel]. \"$errorMessage\" with file \"$errorFile:$errorLine\""));
        });
    }
}
