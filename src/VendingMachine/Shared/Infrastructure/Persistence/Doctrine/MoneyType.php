<?php

namespace App\VendingMachine\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Utils;
use App\Shared\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Persistence\Doctrine\Type\DoctrineCustomType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

use function Lambdish\Phunctional\last;

class MoneyType extends StringType implements DoctrineCustomType
{
    final public static function customTypeName(): string
    {
        return Utils::toSnakeCase(
            str_replace('Type', '', (string)last(explode('\\', static::class)))
        );
    }

    final public function getName(): string
    {
        return self::customTypeName();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Money
    {
        $className = Money::class;
        /** @var Money $className */
        return $className::fromCents($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        /** @var Money $value */
        return $value->cents();
    }
}
