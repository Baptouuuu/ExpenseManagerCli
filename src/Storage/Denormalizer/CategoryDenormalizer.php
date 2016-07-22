<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\Cli\{
    Entity\Category\Identity,
    Storage\DenormalizerInterface
};
use ExpenseManager\{
    Color,
    Entity\Category
};
use Innmind\Reflection\ReflectionClass;

final class CategoryDenormalizer implements DenormalizerInterface
{
    public function denormalize(array $data)
    {
        $data = [
            'identity' => new Identity($data['identity']),
            'name' => $data['name'],
            'color' => new Color($data['color']),
        ];

        return (new ReflectionClass(Category::class))
            ->withProperties($data)
            ->buildObject();
    }
}
