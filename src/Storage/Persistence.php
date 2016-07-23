<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage;

use ExpenseManager\Cli\Exception\InvalidArgumentException;
use Innmind\Filesystem\{
    AdapterInterface,
    File,
    Stream\StringStream
};
use Innmind\Immutable\{
    MapInterface,
    Set,
    SetInterface
};

final class Persistence
{
    private $adapters;
    private $normalizers;
    private $denormalizers;
    private $uow;

    public function __construct(
        MapInterface $adapters,
        MapInterface $normalizers,
        MapInterface $denormalizers,
        UnitOfWork $uow
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
        $this->uow = $uow;
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

        if ($this->uow->handles($class)) {
            $this->uow->add($pair->key(), $object);
        }

        return $this;
    }

    public function has(string $class, string $id): bool
    {
        if ($this->uow->has($class, $id)) {
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
        if ($this->uow->has($class, $id)) {
            return $this->uow->get($class, $id);
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

        if ($this->uow->handles($class)) {
            $this->uow->add($id, $object);
        }

        return $object;
    }

    public function remove(string $class, string $id): self
    {
        $adapter = $this->adapters->get($class);
        $this->uow->remove($class, $id);

        if ($adapter->has($id)) {
            $adapter->remove($id);
        }

        return $this;
    }

    /**
     * @param string $class
     *
     * @return SetInterface<$class>
     */
    public function all(string $class): SetInterface
    {
        return $this
            ->adapters
            ->get($class)
            ->all()
            ->reduce(
                new Set($class),
                function(Set $carry, string $id): Set {
                    return $carry->add(
                        $this->get((string) $carry->type(), $id)
                    );
                }
            );
    }
}
