<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage\Denormalizer;

use ExpenseManagerCli\{
    Entity\OneOffIncome\Identity,
    Storage\DenormalizerInterface
};
use ExpenseManager\{
    Amount,
    Entity\OneOffIncome
};
use Innmind\Reflection\ReflectionClass;

final class OneOffIncomeDenormalizer implements DenormalizerInterface
{
    public function denormalize(array $data)
    {
        $data = [
            'identity' => new Identity($data['identity']),
            'amount' => new Amount($data['amount']),
            'date' => new \DateTimeImmutable($data['date']),
            'note' => $data['note'],
        ];

        return (new ReflectionClass(OneOffIncome::class))
            ->withProperties($data)
            ->buildObject();
    }
}
