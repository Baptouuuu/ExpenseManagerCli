<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\Cli\{
    Entity\Expense\Identity,
    Entity\Category\Identity as Category,
    Storage\DenormalizerInterface
};
use ExpenseManager\{
    Amount,
    Entity\Expense
};
use Innmind\Reflection\ReflectionClass;

final class ExpenseDenormalizer implements DenormalizerInterface
{
    public function denormalize(array $data)
    {
        $data = [
            'identity' => new Identity($data['identity']),
            'amount' => new Amount($data['amount']),
            'category' => new Category($data['category']),
            'date' => new \DateTimeImmutable($data['date']),
            'note' => $data['note'],
        ];

        return (new ReflectionClass(Expense::class))
            ->withProperties($data)
            ->buildObject();
    }
}
