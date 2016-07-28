<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\CommandBus;

use ExpenseManager\Cli\Exception\InvalidArgumentException;
use Innmind\CommandBus\{
    CommandBusInterface,
    Exception\InvalidArgumentException as InvalidCommandException
};
use Innmind\Filesystem\LazyAdapterInterface;
use Innmind\Immutable\SetInterface;

final class PersistAdaptersCommandBus implements CommandBusInterface
{
    private $bus;
    private $adapters;

    public function __construct(
        CommandBusInterface $bus,
        SetInterface $adapters
    ) {
        if ((string) $adapters->type() !== LazyAdapterInterface::class) {
            throw new InvalidArgumentException;
        }

        $this->bus = $bus;
        $this->adapters = $adapters;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        if (!is_object($command)) {
            throw new InvalidCommandException;
        }

        $this->bus->handle($command);
        $this
            ->adapters
            ->foreach(function(LazyAdapterInterface $adapter) {
                $adapter->persist();
            });
    }
}
