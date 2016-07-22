<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Factory;

use ExpenseManager\Cli\Storage\UnitOfWork;
use Innmind\Immutable\Set;

final class UnitOfWorkFactory
{
    public static function make(array $classes): UnitOfWork
    {
        $set = new Set('string');

        foreach ($classes as $class) {
            $set = $set->add($class);
        }

        return new UnitOfWork($set);
    }
}
