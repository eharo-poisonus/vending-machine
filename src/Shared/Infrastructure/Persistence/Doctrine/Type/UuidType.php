<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Domain\Utils;
use App\Shared\Domain\ValueObject\Uuid;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

use function Lambdish\Phunctional\last;

abstract class UuidType extends StringType implements DoctrineCustomType
{
    abstract protected function typeClassName(): string;

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

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        $className = $this->typeClassName();
        /** @var Uuid $className */
        return $className::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        /** @var Uuid $value */
        return $value->value();
    }
}
