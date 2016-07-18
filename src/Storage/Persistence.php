<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage;

use ExpenseManagerCli\Exception\InvalidArgumentException;
use Innmind\Filesystem\{
    AdapterInterface,
    File,
    Stream\StringStream
};
use Innmind\Immutable\{
    MapInterface,
    Map
};

final class Persistence
{
    private $adapters;
    private $normalizers;
    private $denormalizers;
    private $objects;

    public function __construct(
        MapInterface $adapters,
        MapInterface $normalizers,
        MapInterface $denormalizers
    ) {
        if (
            (string) $adapters->keyType() !== 'string' ||
            (string) $adapters->valueType() !== AdapterInterface::class ||
            (string) $normalizers->keyType() !== 'string' ||
            (string) $normalizers->valueType() !== NormalizerInterface::class ||
            (string) $denormalizers->keyType() !== 'string' ||
            (string) $denormalizers->valueType() !== DenormalizerInterface::class
        ) {
            throw new InvalidArgumentException;
        }

        $this->adapters = $adapters;
        $this->normalizers = $normalizers;
        $this->denormalizers = $denormalizers;
        $this->objects = new Map('string', 'object');
    }

    /**
     * @param object $object
     *
     * @return self
     */
    public function persist($object): self
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException;
        }

        $class = get_class($object);
        $pair = $this
            ->normalizers
            ->get($class)
            ->normalize($object);
        $this
            ->adapters
            ->get($class)
            ->add(
                new File(
                    $pair->key(),
                    new StringStream(json_encode($pair->value())."\n")
                )
            );
        $this->objects = $this->objects->put(
            $class.'#'.$pair->key(),
            $object
        );

        return $this;
    }

    public function has(string $class, string $id): bool
    {
        if ($this->objects->contains($class.'#'.$id)) {
            return true;
        }

        return $this
            ->adapters
            ->get($class)
            ->has($id);
    }

    /**
     * @param string $class
     * @param string $id
     *
     * @return object
     */
    public function get(string $class, string $id)
    {
        if ($this->objects->contains($class.'#'.$id)) {
            return $this->objects->get($class.'#'.$id);
        }

        $file = $this
            ->adapters
            ->get($class)
            ->get($id);

        $object = $this
            ->denormalizers
            ->get($class)
            ->denormalize(json_decode(
                (string) $file->content(),
                true
            ));
        $this->objects = $this->objects->put(
            $class.'#'.$id,
            $object
        );

        return $object;
    }
}
