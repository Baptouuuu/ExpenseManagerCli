<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage\Normalizer;

use ExpenseManagerCli\{
    Storage\NormalizerInterface,
    Exception\InvalidArgumentException
};
use ExpenseManager\Entity\Category;
use Innmind\Immutable\Pair;

final class CategoryNormalizer implements NormalizerInterface
{
    public function normalize($category): Pair
    {
        if (!$category instanceof Category) {
            throw new InvalidArgumentException;
        }

        return new Pair(
            (string) $category->identity(),
            [
                'identity' => (string) $category->identity(),
                'name' => $category->name(),
                'color' => (string) $category->color(),
            ]
        );
    }
}
