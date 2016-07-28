<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage\Normalizer;

use ExpenseManager\{
    Cli\Storage\NormalizerInterface,
    Cli\Exception\InvalidArgumentException,
    Entity\MonthReport
};
use Innmind\Immutable\Pair;

final class MonthReportNormalizer implements NormalizerInterface
{
    public function normalize($report): Pair
    {
        if (!$report instanceof MonthReport) {
            throw new InvalidArgumentException;
        }

        $refl = new \ReflectionProperty(MonthReport::class, 'appliedIncomes');
        $refl->setAccessible(true);
        $appliedIncomes = $refl->getValue($report);
        $refl->setAccessible(false);
        $refl = new \ReflectionProperty(MonthReport::class, 'appliedFixedCosts');
        $refl->setAccessible(true);
        $appliedFixedCosts = $refl->getValue($report);
        $refl->setAccessible(false);

        return new Pair(
            (string) $report->identity(),
            [
                'identity' => (string) $report->identity(),
                'date' => (string) $report,
                'amount' => $report->amount()->value(),
                'appliedIncomes' => $appliedIncomes->toPrimitive(),
                'appliedFixedCosts' => $appliedFixedCosts->toPrimitive(),
            ]
        );
    }
}
