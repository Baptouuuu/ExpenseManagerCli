<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Repository;

use ExpenseManager\Cli\Storage\Persistence;
use ExpenseManager\{
    Repository\MonthReportRepositoryInterface,
    Entity\MonthReport,
    Entity\MonthReport\IdentityInterface,
    Exception\MonthReportNotFoundException
};
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    Set,
    SetInterface
};

final class MonthReportRepository implements MonthReportRepositoryInterface
{
    private $persistence;

    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function add(MonthReport $income): MonthReportRepositoryInterface
    {
        $this->persistence->persist($income);

        return $this;
    }

    public function get(IdentityInterface $identity): MonthReport
    {
        if (!$this->has($identity)) {
            throw new MonthReportNotFoundException;
        }

        return $this->persistence->get(MonthReport::class, (string) $identity);
    }

    public function has(IdentityInterface $identity): bool
    {
        return $this->persistence->has(MonthReport::class, (string) $identity);
    }

    public function remove(IdentityInterface $identity): MonthReportRepositoryInterface
    {
        $this->persistence->remove(MonthReport::class, (string) $identity);
    }

    /**
     * @return SetInterface<MonthReport>
     */
    public function all(): SetInterface
    {
        return $this->persistence->all(MonthReport::class);
    }

    /**
     * @return SetInterface<MonthReport>
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return $this
            ->all()
            ->filter(function(MonthReport $income) use ($specification) {
                return $specification->isSatisfiedBy($income);
            });
    }
}
