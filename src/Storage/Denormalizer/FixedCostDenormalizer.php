<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\Cli\{
    Entity\FixedCost\Identity,
    Entity\Category\Identity as Category,
    Storage\DenormalizerInterface
};
use ExpenseManager\{
    Amount,
    ApplyDay,
    Entity\FixedCost
};
use Innmind\Reflection\ReflectionClass;

final class FixedCostDenormalizer implements DenormalizerInterface
{
    public function denormalize(array $data)
    {
        $data = [
            'identity' => new Identity($data['identity']),
            'name' => $data['name'],
            'amount' => new Amount($data['amount']),
            'applyDay' => new ApplyDay($data['applyDay']),
            'category' => new Category($data['category']),
        ];

        return (new ReflectionClass(FixedCost::class))
            ->withProperties($data)
            ->buildObject();
    }
}
