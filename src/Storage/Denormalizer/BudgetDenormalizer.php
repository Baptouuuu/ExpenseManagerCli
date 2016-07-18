<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage\Denormalizer;

use ExpenseManagerCli\{
    Entity\Budget\Identity,
    Entity\Category\Identity as Category,
    Storage\DenormalizerInterface
};
use ExpenseManager\{
    Entity\Budget,
    Entity\Category\IdentityInterface,
    Amount
};
use Innmind\Reflection\ReflectionClass;
use Innmind\Immutable\Set;

final class BudgetDenormalizer implements DenormalizerInterface
{
    public function denormalize(array $data)
    {
        $categories = new Set(IdentityInterface::class);

        foreach ($data['categories'] as $uuid) {
            $categories = $categories->add(new Category($uuid));
        }

        $data = [
            'identity' => new Identity($data['identity']),
            'name' => $data['name'],
            'amount' => new Amount($data['amount']),
            'categories' => $categories,
        ];

        return (new ReflectionClass(Budget::class))
            ->withProperties($data)
            ->buildObject();
    }
}
