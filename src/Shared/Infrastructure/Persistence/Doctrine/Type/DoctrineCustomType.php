<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Type;

interface DoctrineCustomType
{
    public static function customTypeName(): string;
}
