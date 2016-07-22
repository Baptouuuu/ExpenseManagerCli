<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Repository;

use ExpenseManager\Cli\Storage\Persistence;
use ExpenseManager\{
    Repository\OneOffIncomeRepositoryInterface,
    Entity\OneOffIncome,
    Entity\OneOffIncome\IdentityInterface,
    Exception\OneOffIncomeNotFoundException
};
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    Set,
    SetInterface
};

final class OneOffIncomeRepository implements OneOffIncomeRepositoryInterface
{
    private $persistence;

    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function add(OneOffIncome $income): OneOffIncomeRepositoryInterface
    {
        $this->persistence->persist($income);

        return $this;
    }

    public function get(IdentityInterface $identity): OneOffIncome
    {
        if (!$this->has($identity)) {
            throw new OneOffIncomeNotFoundException;
        }

        return $this->persistence->get(OneOffIncome::class, (string) $identity);
    }

    public function has(IdentityInterface $identity): bool
    {
        return $this->persistence->has(OneOffIncome::class, (string) $identity);
    }

    public function remove(IdentityInterface $identity): OneOffIncomeRepositoryInterface
    {
        $this->persistence->remove(OneOffIncome::class, (string) $identity);
    }

    /**
     * @return SetInterface<OneOffIncome>
     */
    public function all(): SetInterface
    {
        return $this->persistence->all(OneOffIncome::class);
    }

    /**
     * @return SetInterface<OneOffIncome>
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return $this
            ->all()
            ->filter(function(OneOffIncome $income) use ($specification) {
                return $specification->isSatisfiedBy($income);
            });
    }
}
