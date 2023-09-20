<?php

namespace Alpha\Http;

use Alpha\Contracts\{
    HttpKernelInterface,
    HttpRouterInterface,
};
use RuntimeException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

class HttpKernel implements HttpKernelInterface
{
    public function __construct(
        private RequestInterface $request,
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

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        if ($response instanceof JsonResponse) {
            $this->response->setHeader('Content-Type', 'application/json');

            $this->response->setBody(json_encode($response->data));

            return $this->response;
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
