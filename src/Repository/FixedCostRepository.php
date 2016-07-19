<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Repository;

use ExpenseManagerCli\Storage\Persistence;
use ExpenseManager\{
    Repository\FixedCostRepositoryInterface,
    Entity\FixedCost,
    Entity\FixedCost\IdentityInterface,
    Exception\FixedCostNotFoundException
};
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\Set;

final class FixedCostRepository implements FixedCostRepositoryInterface
{
    private $persistence;

    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function add(FixedCost $cost): FixedCostRepositoryInterface
    {
        $this->persistence->persist($cost);

        return $this;
    }

    public function get(IdentityInterface $identity): FixedCost
    {
        if (!$this->has($identity)) {
            throw new FixedCostNotFoundException;
        }

        return $this->persistence->get(FixedCost::class, (string) $identity);
    }

    public function has(IdentityInterface $identity): bool
    {
        return $this->persistence->has(FixedCost::class, (string) $identity);
    }

    public function remove(IdentityInterface $identity): FixedCostRepositoryInterface
    {
        $this->persistence->remove(FixedCost::class, (string) $identity);
    }

    /**
     * @return SetInterface<FixedCost>
     */
    public function all(): SetInterface
    {
        return $this
            ->persistence
            ->all()
            ->filter(function(string $key, $entity): bool {
                return $entity instanceof FixedCost;
            })
            ->reduce(
                new Set(FixedCost::class),
                function(Set $carry, string $key, FixedCost $cost): Set {
                    return $carry->add($cost);
                }
            );
    }

    /**
     * @return SetInterface<FixedCost>
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return $this
            ->all()
            ->filter(function(FixedCost $cost) use ($specification) {
                return $specification->isSatisfiedBy($cost);
            });
    }
}
