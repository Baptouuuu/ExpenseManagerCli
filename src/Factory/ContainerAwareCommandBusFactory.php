<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Factory;

use ExpenseManager\Cli\CommandBus\ContainerAwareCommandBus;
use Innmind\Immutable\Map;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerAwareCommandBusFactory
{
    public static function make(
        ContainerInterface $container,
        array $mapping
    ): ContainerAwareCommandBus {
        $map = new Map('string', 'string');

        foreach ($mapping as $class => $service) {
            $map = $map->put($class, $service);
        }

        return new ContainerAwareCommandBus($container, $map);
    }
}
