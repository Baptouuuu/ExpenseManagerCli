<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Repository;

use ExpenseManager\Cli\Storage\Persistence;
use ExpenseManager\{
    Repository\ExpenseRepositoryInterface,
    Entity\Expense,
    Entity\Expense\IdentityInterface,
    Exception\ExpenseNotFoundException
};
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    Set,
    SetInterface
};

final class ExpenseRepository implements ExpenseRepositoryInterface
{
    private $persistence;

    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function add(Expense $expense): ExpenseRepositoryInterface
    {
        $this->persistence->persist($expense);

        return $this;
    }

    public function get(IdentityInterface $identity): Expense
    {
        if (!$this->has($identity)) {
            throw new ExpenseNotFoundException;
        }

        return $this->persistence->get(Expense::class, (string) $identity);
    }

    public function has(IdentityInterface $identity): bool
    {
        return $this->persistence->has(Expense::class, (string) $identity);
    }

    public function remove(IdentityInterface $identity): ExpenseRepositoryInterface
    {
        $this->persistence->remove(Expense::class, (string) $identity);
    }

    /**
     * @return SetInterface<Expense>
     */
    public function all(): SetInterface
    {
        return $this->persistence->all(Expense::class);
    }

    /**
     * @return SetInterface<Expense>
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return $this
            ->all()
            ->filter(function(Expense $expense) use ($specification) {
                return $specification->isSatisfiedBy($expense);
            });
    }
}
