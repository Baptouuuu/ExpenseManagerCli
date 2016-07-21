<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage;

use ExpenseManagerCli\Exception\InvalidArgumentException;
use Innmind\Filesystem\{
    AdapterInterface,
    File,
    Stream\StringStream,
    Directory
};
use Innmind\Immutable\{
    MapInterface,
    Map
};

final class Persistence
{
    private $filesystem;
    private $folders;
    private $normalizers;
    private $denormalizers;
    private $uow;

    public function __construct(
        AdapterInterface $filesystem,
        MapInterface $folders,
        MapInterface $normalizers,
        MapInterface $denormalizers,
        UnitOfWork $uow
    ) {
        if (
            (string) $folders->keyType() !== 'string' ||
            (string) $folders->valueType() !== 'string' ||
            (string) $normalizers->keyType() !== 'string' ||
            (string) $normalizers->valueType() !== NormalizerInterface::class ||
            (string) $denormalizers->keyType() !== 'string' ||
            (string) $denormalizers->valueType() !== DenormalizerInterface::class
        ) {
            throw new InvalidArgumentException;
        }

        $this->filesystem = $filesystem;
        $this->folders = $folders;
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
        $folder = $this->folders->get($class);

        if (!$this->filesystem->has($folder)) {
            $this->filesystem->add(new Directory($folder));
        }

        $directory = $this->filesystem->get($folder);
        $this->filesystem->add($directory->add(
            new File(
                $pair->key(),
                new StringStream(json_encode($pair->value())."\n")
            )
        ));

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

        $folder = $this->folders->get($class);

        if (!$this->filesystem->has($folder)) {
            return false;
        }

        return $this
            ->filesystem
            ->get($folder)
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
            ->filesystem
            ->get($this->folders->get($class))
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
        $this->uow->remove($class, $id);
        $adapter = $this->folders->get($class);
        $folder = $this->folders->get($class);

        if (!$this->filesystem->has($folder)) {
            return $this;
        }

        $directory = $this
            ->filesystem
            ->get($folder);

        if ($directory->has($id)) {
            $this->filesystem->add($directory->remove($id));
        }

        return $this;
    }
}
