<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Repository;

use ExpenseManager\Cli\Storage\Persistence;
use ExpenseManager\{
    Repository\BudgetRepositoryInterface,
    Entity\Budget,
    Entity\Budget\IdentityInterface,
    Exception\BudgetNotFoundException
};
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    Set,
    SetInterface
};

final class BudgetRepository implements BudgetRepositoryInterface
{
    private $persistence;

    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function add(Budget $budget): BudgetRepositoryInterface
    {
        $this->persistence->persist($budget);

        return $this;
    }

    public function get(IdentityInterface $identity): Budget
    {
        if (!$this->has($identity)) {
            throw new BudgetNotFoundException;
        }

        return $this->persistence->get(Budget::class, (string) $identity);
    }

    public function has(IdentityInterface $identity): bool
    {
        return $this->persistence->has(Budget::class, (string) $identity);
    }

    public function remove(IdentityInterface $identity): BudgetRepositoryInterface
    {
        $this->persistence->remove(Budget::class, (string) $identity);
    }

    /**
     * @return SetInterface<Budget>
     */
    public function all(): SetInterface
    {
        return $this->persistence->all(Budget::class);
    }

    /**
     * @return SetInterface<Budget>
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return $this
            ->all()
            ->filter(function(Budget $budget) use ($specification) {
                return $specification->isSatisfiedBy($budget);
            });
    }
}
