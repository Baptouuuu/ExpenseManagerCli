<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Factory;

use Innmind\CommandBus\CommandBus;
use Innmind\Immutable\Map;

final class CommandBusFactory
{
    public static function make(array $mapping): CommandBus
    {
        $map = new Map('string', 'callable');

        foreach ($mapping as $class => $handler) {
            $map = $map->put($class, $handler);
        }

        return new CommandBus($map);
    }
}
