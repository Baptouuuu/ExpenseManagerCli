<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage;

use Innmind\Filesystem\Adapter\FilesystemAdapter as Adapter;
use Symfony\Component\Filesystem\Filesystem;

final class FilesystemAdapter extends Adapter
{
    public function __construct(string $path)
    {
        parent::__construct($path);
        (new Filesystem)->mkdir($path);
    }
}
