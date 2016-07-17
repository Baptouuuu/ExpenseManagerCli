<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage\Normalizer;

use ExpenseManagerCli\{
    Storage\NormalizerInterface,
    Exception\InvalidArgumentException
};
use ExpenseManager\Entity\OneOffIncome;
use Innmind\Immutable\Pair;

final class OneOffIncomeNormalizer implements NormalizerInterface
{
    public function normalize($income): Pair
    {
        if (!$income instanceof OneOffIncome) {
            throw new InvalidArgumentException;
        }

        return new Pair(
            (string) $income->identity(),
            [
                'identity' => (string) $income->identity(),
                'amount' => $income->amount()->value(),
                'date' => $income->date()->format(\DateTime::ISO8601),
                'note' => $income->note(),
            ]
        );
    }
}
