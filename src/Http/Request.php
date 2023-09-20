<?php

namespace Alpha\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;

class Request implements RequestInterface
{
    use MessageTrait;

    public function __construct(
        private UriInterface $uri,
        StreamInterface|null $stream = null,
        private string $method = '',
        array $headers = [],
        string $protocol = '1.1'
    )  {
        $this->headers = $headers;
        $this->protocol = $protocol;
        $this->stream = $stream;
    }

    public function getRequestTarget(): string
    {
        $target = $this->uri->getPath();

        if ($this->uri->getQuery() !== '') {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $new = clone $this;
        $new->uri = $new->uri->withPath($requestTarget);

        return $new;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        $new = clone $this;
        $new->method = $method;

        return $new;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $new = clone $this;
        $new->uri = $uri;

        if (!$preserveHost) {
            $new = $new->updateHostHeaderFromUri();
        }

        return $new;
    }

    public function server(): array
    {
        return $_SERVER;
    }

    public function post(): array
    {
        return $_POST;
    }

    public function get(): array
    {
        return $_GET;
    }

    private function updateHostHeaderFromUri(): self
    {
        $host = $this->uri->getHost();

        if ($host === '') {
            return $this;
        }

        if (($port = $this->uri->getPort()) !== null) {
            $host .= ':' . $port;
        }

        $new = clone $this;
        $new->headers['Host'] = [$host];

        return $new;
    }
}
