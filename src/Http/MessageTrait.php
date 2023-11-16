<?php

namespace Alpha\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    private array $headerNames = [];
    private array $headers = [];
    private string $protocol = '1.1';
    private StreamInterface|null $stream = null;

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion(string $version): ResponseInterface
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    public function getHeader(string $name): array
    {
        if ($this->hasHeader($name)) {
            return [$this->headers[$name]];
        }

        return [];
    }

    public function getHeaderLine(string $name): string
    {
        return \implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): ResponseInterface
    {
        $new = clone $this;

        if ($new->hasHeader($name)) {
            unset($new->headers[$name]);
        }

        $new->headers[$name] = $value;

        return $new;
    }

    public function withAddedHeader(string $name, $value): ResponseInterface
    {
        $new = clone $this;

        $new->headers[$name] = $new->hasHeader($name) ? "{$new->headers[$name]} , $value" : $value;

        return $new;
    }

    public function withoutHeader(string $name): ResponseInterface
    {
        if ($this->hasHeader($name)) {
            return $this;
        }

        $new = clone $this;
        unset($new->headers[$name]);

        return $new;
    }

    public function getBody(): StreamInterface
    {
        if (null === $this->stream) {
            $this->stream = Stream::create('');
        }

        return $this->stream;

    }

    public function withBody(StreamInterface $body): ResponseInterface
    {
        if ($body === $this->stream) {
            return $this;
        }

        $new = clone $this;
        $new->stream = $body;

        return $new;
    }

    private function setHeaders(array $headers): void
    {
//        $this->headers = \array_merge($this->headers, $headers);

        $this->headerNames = $this->headers = [];
        foreach ($headers as $header => $value) {
            // Numeric array keys are converted to int by PHP.
            $header = (string) $header;

            $value = $this->normalizeHeaderValue($value);
            $normalized = strtolower($header);
            if (isset($this->headerNames[$normalized])) {
                $header = $this->headerNames[$normalized];
                $this->headers[$header] = array_merge($this->headers[$header], $value);
            } else {
                $this->headerNames[$normalized] = $header;
                $this->headers[$header] = $value;
            }
        }
    }

    private function normalizeHeaderValue($value): array
    {
        if (!is_array($value)) {
            return $this->trimAndValidateHeaderValues([$value]);
        }

        if (count($value) === 0) {
            throw new \InvalidArgumentException('Header value can not be an empty array.');
        }

        return $this->trimAndValidateHeaderValues($value);
    }

    private function trimAndValidateHeaderValues(array $values): array
    {
        return array_map(function ($value) {
            if (!is_scalar($value) && null !== $value) {
                throw new \InvalidArgumentException(sprintf(
                    'Header value must be scalar or null but %s provided.',
                    is_object($value) ? get_class($value) : gettype($value)
                ));
            }

            return trim((string) $value, " \t");
        }, array_values($values));
    }
}