<?php

namespace App\Shared\Domain\Criteria\Group;

enum LogicalOperator: string
{
    case AND =  'AND';
    case OR =  'OR';
}
