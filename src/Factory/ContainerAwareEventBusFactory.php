<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Factory;

use ExpenseManager\Cli\EventBus\ContainerAwareEventBus;
use Innmind\Immutable\{
    Map,
    Set,
    SetInterface
};
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerAwareEventBusFactory
{
    public static function make(
        ContainerInterface $container,
        array $listeners
    ): ContainerAwareEventBus {
        $map = new Map('string', SetInterface::class);

        foreach ($listeners as $class => $services) {
            $set = new Set('string');

            foreach ($services as $service) {
                $set = $set->add($service);
            }

            $map = $map->put($class, $set);
        }

        return new ContainerAwareEventBus($container, $map);
    }
}
