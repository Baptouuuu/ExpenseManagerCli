<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\Cli\{
    Entity\Income\Identity,
    Storage\DenormalizerInterface
};
use ExpenseManager\{
    Amount,
    ApplyDay,
    Entity\Income
};
use Innmind\Reflection\ReflectionClass;

final class IncomeDenormalizer implements DenormalizerInterface
{
    public function denormalize(array $data)
    {
        $data = [
            'identity' => new Identity($data['identity']),
            'name' => $data['name'],
            'amount' => new Amount($data['amount']),
            'applyDay' => new ApplyDay($data['applyDay']),
        ];

        return (new ReflectionClass(Income::class))
            ->withProperties($data)
            ->buildObject();
    }
}
