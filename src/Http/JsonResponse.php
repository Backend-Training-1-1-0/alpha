<?php

namespace Alpha\Http;

class JsonResponse
{
    public function __construct(
        public readonly array $data
    )
    {
    }

}
