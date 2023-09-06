<?php

namespace Alpha\Http;

class RouteParameter
{
    public string $name = '';
    public bool $isRequired = true;
    public mixed $defaultValue = null;

    public function __construct(
        private readonly string $param,
    )
    {
        $this->prepareParam();
    }

    private function prepareParam(): void
    {
        $param = $this->param;

        if (str_starts_with($param, '?')) {
            $param = substr($param, 1);
            $this->isRequired = false;
        }

        if (substr_count($param, '=') === 1 && str_ends_with($param, '=') === false) {
            $argument = explode('=', $param);
            $this->defaultValue = $argument[1];
            $param = $argument[0];
        }

        $this->name = $param;
    }
}