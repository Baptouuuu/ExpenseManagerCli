<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\{
    Cli\Storage\DenormalizerInterface,
    Cli\Entity\MonthReport\Identity,
    Entity\MonthReport,
    Amount
};
use Innmind\Immutable\Set;
use Innmind\Reflection\ReflectionClass;

final class MonthReportDenormalizer implements DenormalizerInterface
{
    public function denormalize(array $data)
    {
        $appliedIncomes = new Set('string');
        $appliedFixedCosts = new Set('string');

        foreach ($data['appliedIncomes'] as $identity) {
            $appliedIncomes = $appliedIncomes->add($identity);
        }

        foreach ($data['appliedFixedCosts'] as $identity) {
            $appliedFixedCosts = $appliedFixedCosts->add($identity);
        }

        return (new ReflectionClass(MonthReport::class))
            ->withProperties([
                'identity' => new Identity($data['identity']),
                'date' => new \DateTimeImmutable($data['date']),
                'amount' => new Amount($data['amount']),
                'appliedIncomes' => $appliedIncomes,
                'appliedFixedCosts' => $appliedFixedCosts,
            ])
            ->buildObject();
    }
}
