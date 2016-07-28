<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Factory;

use ExpenseManager\Cli\CommandBus\PersistAdaptersCommandBus;
use Innmind\CommandBus\CommandBusInterface;
use Innmind\Filesystem\LazyAdapterInterface;
use Innmind\Immutable\Set;

final class PersistAdaptersCommandBusFactory
{
    public static function make(
        CommandBusInterface $bus,
        array $adapters
    ): PersistAdaptersCommandBus {
        $set = new Set(LazyAdapterInterface::class);

        foreach ($adapters as $adapter) {
            $set = $set->add($adapter);
        }

        return new PersistAdaptersCommandBus($bus, $set);
    }
}
