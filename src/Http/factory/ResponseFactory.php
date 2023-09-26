<?php

namespace Alpha\Http\factory;

use Alpha\Http\JsonResponse;
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
};
use Alpha\Http\Response;

class ResponseFactory implements ResponseFactoryInterface
{
    public function createResponse(int $code = 200, string $reasonPhrase = '', string $responseType = ''): ResponseInterface
    {
        return match ($responseType) {
            'json' => new JsonResponse(null, $code, [] , '1.1', $reasonPhrase),
            default => new Response(null, $code, [] , '1.1', $reasonPhrase),
        };
    }
}