<?php

namespace Alpha\Http;

use Alpha\Contracts\{
    HttpResponseInterface,
    HttpKernelInterface,
    HttpRequestInterface,
    HttpRouterInterface,
};

class HttpKernel implements HttpKernelInterface
{
    public function __construct(
        private HttpRequestInterface $request,
        private HttpResponseInterface $response,
        private HttpRouterInterface $router,
    ) {}

    public function handle(): HttpResponseInterface
    {
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
}
