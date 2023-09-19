<?php

namespace Alpha\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements HttpResponseInterface
{
    private array $headers = [];
    private string|null $body = null;
    private int $statusCode = 200;
    private string $reasonPhrase = '';

    public function __construct() { }

    public function send(): never
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->body;

        exit;
    }

    public function getProtocolVersion(): string
    {
        return '1.1';
    }

    public function withProtocolVersion($version): ResponseInterface
    {
        // Not implemented
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        return isset($this->headers[$name]);
    }

    public function getHeader($name): array
    {
        if ($this->hasHeader($name)) {
            return [$this->headers[$name]];
        }

        return [];
    }

    public function getHeaderLine($name): string
    {
        if ($this->hasHeader($name)) {
            return $this->headers[$name];
        }

        return '';
    }

    public function withHeader($name, $value): ResponseInterface
    {
        $new = clone $this;
        $new->headers[$name] = $value;

        return $new;
    }

    public function withAddedHeader($name, $value): ResponseInterface
    {
        $new = clone $this;

        if ($this->hasHeader($name)) {
            $new->headers[$name] .= ', ' . $value;
        } else {
            $new->headers[$name] = $value;
        }

        return $new;
    }

    public function withoutHeader($name): ResponseInterface
    {
        $new = clone $this;
        unset($new->headers[$name]);

        return $new;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = ''): ResponseInterface
    {
        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = $reasonPhrase;

        return $new;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function getBody(): StreamInterface
    {
        // Not implemented
        return null;
    }

    public function withBody(StreamInterface $body): ResponseInterface
    {
        // Not implemented
        return $this;
    }
}
