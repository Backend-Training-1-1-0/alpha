<?php

namespace Http;

class JsonResponse
{
    public function __construct(
        public readonly array $data
    )
    {
    }

}
