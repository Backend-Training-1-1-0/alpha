<?php

namespace Alpha\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    public function __construct(
        private string $scheme = '',
        private string $host = '',
        private ?int $port = null,
        private string $path = '',
        private string $query = '',
        private string $fragment = '',
        private string $userInfo = '',
    ) { }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        $authority = $this->host;

        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme(string $scheme): UriInterface
    {
        $new = clone $this;
        $new->scheme = $scheme;

        return $new;
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $new = clone $this;
        $new->userInfo = $user;

        if ($password !== null) {
            $new->userInfo .= ':' . $password;
        }

        return $new;
    }

    public function withHost(string $host): UriInterface
    {
        $new = clone $this;
        $new->host = $host;

        return $new;
    }

    public function withPort(?int $port): UriInterface
    {
        $new = clone $this;
        $new->port = $port;

        return $new;
    }

    public function withPath(string $path): UriInterface
    {
        $new = clone $this;
        $new->path = $path;

        return $new;
    }

    public function withQuery(string $query): UriInterface
    {
        $new = clone $this;
        $new->query = $query;

        return $new;
    }

    public function withFragment(string $fragment): UriInterface
    {
        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    public function __toString(): string
    {
        $uri = '';

        if ($this->scheme !== '') {
            $uri .= $this->scheme . ':';
        }

        $authority = $this->getAuthority();
        if ($authority !== '') {
            $uri .= '//' . $authority;
        }

        $uri .= $this->path;
        if ($this->query !== '') {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment !== '') {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }
}