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
}
