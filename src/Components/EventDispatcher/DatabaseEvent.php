<?php

namespace Alpha\Components\EventDispatcher;

enum DatabaseEvent: string
{
    case SQL_CREATED = 'sql_created';
}
