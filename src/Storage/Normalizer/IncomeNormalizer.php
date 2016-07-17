<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage\Normalizer;

use ExpenseManagerCli\{
    Storage\NormalizerInterface,
    Exception\InvalidArgumentException
};
use ExpenseManager\Entity\Income;
use Innmind\Immutable\Pair;

final class IncomeNormalizer implements NormalizerInterface
{
    public function normalize($income): Pair
    {
        if (!$income instanceof Income) {
            throw new InvalidArgumentException;
        }

        return new Pair(
            (string) $income->identity(),
            [
                'identity' => (string) $income->identity(),
                'name' => $income->name(),
                'amount' => $income->amount()->value(),
                'applyDay' => $income->applyDay()->value(),
            ]
        );
    }
}
