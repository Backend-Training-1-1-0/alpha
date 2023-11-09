<?php

namespace Alpha\Http;

use Psr\Http\Message\{
    ServerRequestInterface,
    UploadedFileInterface,
    UriInterface,
};

class RequestServer implements ServerRequestInterface
{
    use MessageTrait;
    use RequestTrait;

    private array $attributes = [];

    private array $cookieParams = [];

    private array|object|null $parsedBody;

    private array $queryParams = [];

    private array $serverParams = [];

    /** @var UploadedFileInterface[] */
    private array $uploadedFiles = [];

    public function __construct(
        UriInterface $uri,
        string $method = '',
        array $headers = [],
        mixed $body = null,
        string $version = '1.1',
        array $serverParams = [],
    )
    {
        $this->serverParams = count($serverParams) > 0 ? $serverParams : $_SERVER;

        if (false === ($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method = $method;
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;

        $this->queryParams = $_GET;

        if (!$this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }

        if ('' !== $body && null !== $body) {
            $this->stream = Stream::create($body);
        }
    }

    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles): static
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;

        return $new;
    }

    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies): static
    {
        $new = clone $this;
        $new->cookieParams = $cookies;

        return $new;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query): static
    {
        $new = clone $this;
        $new->queryParams = $query;

        return $new;
    }

    public function getParsedBody(): array|object|null
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data): static
    {
        $new = clone $this;
        $new->parsedBody = $data;

        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $attribute, $default = null): mixed
    {
        if (false === array_key_exists($attribute, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$attribute];
    }

    public function withAttribute(string $attribute, $value): static
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;

        return $new;
    }

    public function withoutAttribute(string $attribute): static
    {
        if (false === array_key_exists($attribute, $this->attributes)) {
            return $this;
        }

        $new = clone $this;
        unset($new->attributes[$attribute]);

        return $new;
    }
}