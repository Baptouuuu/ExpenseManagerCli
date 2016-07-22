<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Repository;

use ExpenseManager\Cli\Storage\Persistence;
use ExpenseManager\{
    Repository\IncomeRepositoryInterface,
    Entity\Income,
    Entity\Income\IdentityInterface,
    Exception\IncomeNotFoundException
};
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    Set,
    SetInterface
};

final class IncomeRepository implements IncomeRepositoryInterface
{
    private $persistence;

    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function add(Income $income): IncomeRepositoryInterface
    {
        $this->persistence->persist($income);

        return $this;
    }

    public function get(IdentityInterface $identity): Income
    {
        if (!$this->has($identity)) {
            throw new IncomeNotFoundException;
        }

        return $this->persistence->get(Income::class, (string) $identity);
    }

    public function has(IdentityInterface $identity): bool
    {
        return $this->persistence->has(Income::class, (string) $identity);
    }

    public function remove(IdentityInterface $identity): IncomeRepositoryInterface
    {
        $this->persistence->remove(Income::class, (string) $identity);
    }

    /**
     * @return SetInterface<Income>
     */
    public function all(): SetInterface
    {
        return $this->persistence->all(Income::class);
    }

    /**
     * @return SetInterface<Income>
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return $this
            ->all()
            ->filter(function(Income $income) use ($specification) {
                return $specification->isSatisfiedBy($income);
            });
    }
}
