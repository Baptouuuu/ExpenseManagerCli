<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Factory;

use ExpenseManagerCli\Storage\{
    Persistence,
    UnitOfWork,
    NormalizerInterface,
    DenormalizerInterface
};
use Innmind\Filesystem\AdapterInterface;
use Innmind\Immutable\Map;

final class PersistenceFactory
{
    public static function make(
        array $adapters,
        array $normalizers,
        array $denormalizers,
        UnitOfWork $uow
    ): Persistence {
        return new Persistence(
            array_reduce(
                array_keys($adapters),
                function(Map $carry, string $class) use ($adapters): Map {
                    return $carry->put($class, $adapters[$class]);
                },
                new Map('string', AdapterInterface::class)
            ),
            array_reduce(
                array_keys($normalizers),
                function(Map $carry, string $class) use ($normalizers): Map {
                    return $carry->put($class, $normalizers[$class]);
                },
                new Map('string', NormalizerInterface::class)
            ),
            array_reduce(
                array_keys($denormalizers),
                function(Map $carry, string $class) use ($denormalizers): Map {
                    return $carry->put($class, $denormalizers[$class]);
                },
                new Map('string', DenormalizerInterface::class)
            ),
            $uow
        );
    }
}
