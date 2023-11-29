<?php

namespace Alpha\Components\Attributes;

#[\Attribute]
class Column
{
    public function __construct(
        public string $name = '',
        public string $type = ''
    ) { }
}