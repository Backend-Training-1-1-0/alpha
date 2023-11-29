<?php

namespace Alpha\Components\EntityManager;

use Alpha\Components\Attributes\Column;
use Alpha\Components\Attributes\Table;

#[Table('test')]
class Test
{
    #[Column(name: 'id', type: 'int')]
    public ?int $id = null;

    #[Column(name: 'test', type: 'string')]
    public string $test;
}