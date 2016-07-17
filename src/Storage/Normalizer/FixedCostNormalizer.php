<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Storage\Normalizer;

use ExpenseManagerCli\{
    Storage\NormalizerInterface,
    Exception\InvalidArgumentException
};
use ExpenseManager\Entity\FixedCost;
use Innmind\Immutable\Pair;

final class FixedCostNormalizer implements NormalizerInterface
{
    public function normalize($fixedCost): Pair
    {
        if (!$fixedCost instanceof FixedCost) {
            throw new InvalidArgumentException;
        }

        return new Pair(
            (string) $fixedCost->identity(),
            [
                'identity' => (string) $fixedCost->identity(),
                'name' => $fixedCost->name(),
                'amount' => $fixedCost->amount()->value(),
                'applyDay' => $fixedCost->applyDay()->value(),
                'category' => (string) $fixedCost->category(),
            ]
        );
    }
}
