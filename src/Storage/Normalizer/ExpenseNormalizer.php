<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Storage\Normalizer;

use ExpenseManager\Cli\{
    Storage\NormalizerInterface,
    Exception\InvalidArgumentException
};
use ExpenseManager\Entity\Expense;
use Innmind\Immutable\Pair;

final class ExpenseNormalizer implements NormalizerInterface
{
    public function normalize($expense): Pair
    {
        if (!$expense instanceof Expense) {
            throw new InvalidArgumentException;
        }

        return new Pair(
            (string) $expense->identity(),
            [
                'identity' => (string) $expense->identity(),
                'amount' => $expense->amount()->value(),
                'category' => (string) $expense->category(),
                'date' => $expense->date()->format(\DateTime::ISO8601),
                'note' => $expense->note(),
            ]
        );
    }
}
