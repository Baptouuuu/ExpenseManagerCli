<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage;

use ExpenseManagerCli\Exception\InvalidArgumentException;
use Innmind\Immutable\{
    MapInterface,
    SetInterface,
    Map
};

final class UnitOfWork
{
    private $classes;
    private $entities;

    public function __construct(SetInterface $classes)
    {
        if ((string) $classes->type() !== 'string') {
            throw new InvalidArgumentException;
        }

        $this->classes = $classes;
        $this->entities = new Map('string', 'object');
    }

    public function handles(string $class): bool
    {
        return $this->classes->contains($class);
    }

    public function add(string $id, $entity): self
    {
        $class = get_class($entity);

        if (!$this->handles($class)) {
            throw new InvalidArgumentException;
        }

        $this->entities = $this->entities->put(
            $class.'#'.$id,
            $entity
        );

        return $this;
    }

    public function has(string $class, string $id): bool
    {
        return $this->entities->contains($class.'#'.$id);
    }

    public function get(string $class, string $id)
    {
        return $this->entities->get($class.'#'.$id);
    }

    public function all(): MapInterface
    {
        return $this->entities;
    }
}
