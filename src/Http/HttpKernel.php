<?php

namespace Alpha\Http;

use Alpha\Contracts\{
    HttpResponseInterface,
    HttpKernelInterface,
    HttpRequestInterface,
    HttpRouterInterface,
};
use RuntimeException;

class HttpKernel implements HttpKernelInterface
{
    public function __construct(
        private HttpRequestInterface $request,
        private HttpResponseInterface $response,
        private HttpRouterInterface $router,
    ) {}

    public function handle(): HttpResponseInterface
    {
        $this->setErrorHandler(function (\Throwable $exception) {
            $this->response->setBody("Код ошибки: {$exception->getCode()} <br> Сообщение: {$exception->getMessage()}");

            $this->response->send();
        });

        $response = $this->router->dispatch($this->request);

        if($response instanceof HttpResponseInterface) {
            return $response;
        }

        if($response instanceof JsonResponse) {
            $this->response->setHeader('Content-Type', 'application/json');

            $this->response->setBody(json_encode($response->data));

            return $this->response;
        }

        $this->response->setBody($response);

        return $this->response;
    }

    private function setErrorHandler(callable $handlerCallback)
    {
        set_exception_handler(function ($exception) use ($handlerCallback) {
            $handlerCallback($exception);
        });

        set_error_handler(function ($errorLevel, $errorMessage, $errorFile, $errorLine) use ($handlerCallback)  {
            $handlerCallback(new RuntimeException("Runtime error [$errorLevel]. \"$errorMessage\" with file \"$errorFile:$errorLine\""));
        });
    }
}
