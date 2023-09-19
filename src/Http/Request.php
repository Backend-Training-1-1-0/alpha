<?php

namespace Alpha\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;

class Request implements HttpRequestInterface
{
    private array $server = [];
    private array $post = [];
    private array $get = [];

    public function __construct() { }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function withMethod($method): RequestInterface
    {
        $new = clone $this;
        $new->server['REQUEST_METHOD'] = $method;

        return $new;
    }

    public function getRequestTarget(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    public function withRequestTarget($requestTarget): RequestInterface
    {
        $new = clone $this;
        $new->server['REQUEST_URI'] = $requestTarget;

        return $new;
    }

    public function getUri(): UriInterface
    {
        // Not implemented
        return null;
    }

    public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
    {
        // Not implemented
        return $this;
    }

    public function getProtocolVersion(): string
    {
        return $this->server['SERVER_PROTOCOL'] ?? '1.1';
    }

    public function withProtocolVersion($version): RequestInterface
    {
        $new = clone $this;
        $new->server['SERVER_PROTOCOL'] = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        // Not implemented
        return [];
    }

    public function hasHeader($name): bool
    {
        // Not implemented
        return false;
    }

    public function getHeader($name): array
    {
        // Not implemented
        return [];
    }

    public function getHeaderLine($name): string
    {
        // Not implemented
        return '';
    }

    public function withHeader($name, $value): RequestInterface
    {
        // Not implemented
        return $this;
    }

    public function withAddedHeader($name, $value): RequestInterface
    {
        // Not implemented
        return $this;
    }

    public function withoutHeader($name): RequestInterface
    {
        // Not implemented
        return $this;
    }

    public function getBody(): StreamInterface
    {
        // Not implemented
        return null;
    }

    public function withBody(StreamInterface $body): RequestInterface
    {
        // Not implemented
        return $this;
    }

    public function server(): array
    {
        return $this->server;
    }

    public function post(): array
    {
        return $this->post;
    }

    public function get(): array
    {
        return $this->get;
    }
}
