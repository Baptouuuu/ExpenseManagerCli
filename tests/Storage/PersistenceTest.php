<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage;

use ExpenseManager\Cli\{
    Storage\Persistence,
    Storage\UnitOfWork,
    Storage\NormalizerInterface,
    Storage\DenormalizerInterface,
    Storage\Normalizer\BudgetNormalizer,
    Storage\Denormalizer\BudgetDenormalizer,
    Entity\Budget\Identity,
    Entity\Category\Identity as Category
};
use ExpenseManager\{
    Entity\Budget,
    Entity\Category\IdentityInterface,
    Amount
};
use Innmind\Filesystem\{
    AdapterInterface,
    Adapter\MemoryAdapter,
    File,
    Stream\StringStream,
    Directory
};
use Innmind\Immutable\{
    Map,
    Set
};
use Ramsey\Uuid\Uuid;

class PersistenceTest extends \PHPUnit_Framework_TestCase
{
    private $persistence;
    private $adapter;

    public function setUp()
    {
        $this->persistence = new Persistence(
            $this->adapter = new MemoryAdapter,
            (new Map('string', 'string'))
                ->put(Budget::class, 'budget'),
            (new Map('string', NormalizerInterface::class))
                ->put(Budget::class, new BudgetNormalizer),
            (new Map('string', DenormalizerInterface::class))
                ->put(Budget::class, new BudgetDenormalizer),
            new UnitOfWork(
                (new Set('string'))->add(Budget::class)
            )
        );
    }

    public function testPersist()
    {
        $uuid = (string) Uuid::uuid4();
        $budget = new Budget(
            new Identity($uuid),
            'foo',
            new Amount(24),
            (new Set(IdentityInterface::class))
                ->add(new Category($category = (string) Uuid::uuid4()))
        );
        $this->assertFalse($this->persistence->has(Budget::class, $uuid));
        $this->assertSame(
            $this->persistence,
            $this->persistence->persist($budget)
        );
        $this->assertTrue($this->persistence->has(Budget::class, $uuid));
        $this->assertSame(
            json_encode([
                'identity' => $uuid,
                'name' => 'foo',
                'amount' => 24,
                'categories' => [$category],
            ])."\n",
            (string) $this->adapter->get('budget')->get($uuid)->content()
        );
        $this->assertSame(
            $budget,
            $this->persistence->get(Budget::class, $uuid)
        );
    }

    public function testGet()
    {
        $uuid = (string) Uuid::uuid4();
        $category = (string) Uuid::uuid4();
        $this->adapter->add(
            (new Directory('budget'))->add(
                new File(
                    $uuid,
                    new StringStream(json_encode([
                        'identity' => $uuid,
                        'name' => 'foo',
                        'amount' => 24,
                        'categories' => [$category],
                    ])."\n")
                )
            )
        );

        $budget = $this->persistence->get(Budget::class, $uuid);

        $this->assertInstanceOf(Budget::class, $budget);
        $this->assertSame($uuid, (string) $budget->identity());
        $this->assertSame('foo', $budget->name());
        $this->assertSame(24, $budget->amount()->value());
        $this->assertSame($category, (string) $budget->categories()->current());
        $this->assertSame(
            $budget,
            $this->persistence->get(Budget::class, $uuid)
        );
    }

    public function testRemove()
    {
        $uuid = (string) Uuid::uuid4();
        $budget = new Budget(
            new Identity($uuid),
            'foo',
            new Amount(24),
            (new Set(IdentityInterface::class))
                ->add(new Category((string) Uuid::uuid4()))
        );
        $this->persistence->persist($budget);

        $this->assertSame(
            $this->persistence,
            $this->persistence->remove(Budget::class, $uuid)
        );
        $this->assertFalse($this->persistence->has(Budget::class, $uuid));
    }
}
