<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventBus;

use ExpenseManager\Cli\Exception\InvalidArgumentException;
use Innmind\EventBus\{
    EventBusInterface,
    EventBus,
    Exception\InvalidArgumentException as InvalidEventArgumentException
};
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Set,
    Map
};

final class ContainerAwareEventBus implements EventBusInterface
{
    private $container;
    private $listeners;
    private $bus;

    public function __construct(
        ContainerInterface $container,
        MapInterface $listeners
    ) {
        if (
            (string) $listeners->keyType() !== 'string' ||
            (string) $listeners->valueType() !== SetInterface::class
        ) {
            throw new InvalidArgumentException;
        }

        $this->container = $container;
        $this->listeners = $listeners;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event): EventBusInterface
    {
        if (!is_object($event)) {
            throw new InvalidEventArgumentException;
        }

        if (!$this->bus instanceof EventBusInterface) {
            $this->initialize();
        }

        $this->bus->dispatch($event);

        return this;
    }

    private function initialize()
    {
        $listeners = $this
            ->listeners
            ->reduce(
                new Map('string', SetInterface::class),
                function(Map $carry, string $class, SetInterface $services): Map {
                    return $carry->put(
                        $class,
                        $services->reduce(
                            new Set('callable'),
                            function(Set $carry, string $service): Set {
                                return $carry->add(
                                    $this->container->get($service)
                                );
                            }
                        )
                    );
                }
            );
        $this->bus = new EventBus($listeners);
    }
}
