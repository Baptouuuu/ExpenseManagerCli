<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage;

use Innmind\Immutable\Pair;

interface NormalizerInterface
{
    /**
     * @return Pair<string, array>
     */
    public function normalize($object): Pair;
}
