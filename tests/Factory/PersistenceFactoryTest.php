<?php
declare(strict_types = 1);

namespace Tests\ExpenseManagerCli\Factory;

use ExpenseManagerCli\{
    Factory\PersistenceFactory,
    Factory\UnitOfWorkFactory,
    Storage\Persistence,
    Storage\Normalizer\CategoryNormalizer,
    Storage\Denormalizer\CategoryDenormalizer
};
use ExpenseManager\Entity\Category;
use Innmind\Filesystem\Adapter\MemoryAdapter;

class PersistenceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $persistence = PersistenceFactory::make(
            [Category::class => new MemoryAdapter],
            [Category::class => new CategoryNormalizer],
            [Category::class => new CategoryDenormalizer],
            UnitOfWorkFactory::make([Category::class])
        );

        $this->assertInstanceOf(Persistence::class, $persistence);
    }
}
