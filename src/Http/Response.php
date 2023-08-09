<?php

namespace Http;

use Contracts\HttpResponseInterface;

class Response implements HttpResponseInterface
{
    private array $headers = [];

    private string|null $body = null;

    public function __construct() { }

    public function send(): never
    {
        foreach ($this->headers as $key => $value) {
            header("$key:$value");
        }

        echo $this->body;
        exit;
    }

    public function setHeader(string $name, string $header): void
    {
        $this->headers[$name] = $header;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }
}
