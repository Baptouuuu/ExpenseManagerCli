<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Repository;

use ExpenseManagerCli\Storage\Persistence;
use ExpenseManager\{
    Repository\CategoryRepositoryInterface,
    Entity\Category,
    Entity\Category\IdentityInterface,
    Exception\CategoryNotFoundException
};
use Innmind\Specification\SpecificationInterface;
use Innmind\Immutable\{
    Set,
    SetInterface
};

final class CategoryRepository implements CategoryRepositoryInterface
{
    private $persistence;

    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    public function add(Category $category): CategoryRepositoryInterface
    {
        $this->persistence->persist($category);

        return $this;
    }

    public function get(IdentityInterface $identity): Category
    {
        if (!$this->has($identity)) {
            throw new CategoryNotFoundException;
        }

        return $this->persistence->get(Category::class, (string) $identity);
    }

    public function has(IdentityInterface $identity): bool
    {
        return $this->persistence->has(Category::class, (string) $identity);
    }

    public function remove(IdentityInterface $identity): CategoryRepositoryInterface
    {
        $this->persistence->remove(Category::class, (string) $identity);
    }

    /**
     * @return SetInterface<Category>
     */
    public function all(): SetInterface
    {
        return $this->persistence->all(Category::class);
    }

    /**
     * @return SetInterface<Category>
     */
    public function matching(SpecificationInterface $specification): SetInterface
    {
        return $this
            ->all()
            ->filter(function(Category $category) use ($specification) {
                return $specification->isSatisfiedBy($category);
            });
    }
}
