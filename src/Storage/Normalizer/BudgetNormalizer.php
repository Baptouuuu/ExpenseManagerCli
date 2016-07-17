<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage\Normalizer;

use ExpenseManagerCli\{
    Storage\NormalizerInterface,
    Exception\InvalidArgumentException
};
use ExpenseManager\Entity\{
    Budget,
    Category\IdentityInterface
};
use Innmind\Immutable\Pair;

final class BudgetNormalizer implements NormalizerInterface
{
    public function normalize($budget): Pair
    {
        if (!$budget instanceof Budget) {
            throw new InvalidArgumentException;
        }

        return new Pair(
            (string) $budget->identity(),
            [
                'identity' => (string) $budget->identity(),
                'name' => $budget->name(),
                'amount' => $budget->amount()->value(),
                'categories' => $budget->categories()->reduce(
                    [],
                    function(array $carry, IdentityInterface $category): array {
                        $carry[] = (string) $category;

                        return $carry;
                    }
                ),
            ]
        );
    }
}
