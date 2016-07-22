<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\Cli\Storage\{
    Denormalizer\BudgetDenormalizer,
    DenormalizerInterface
};
use ExpenseManager\Entity\Budget;
use Ramsey\Uuid\Uuid;

class BudgetDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            DenormalizerInterface::class,
            new BudgetDenormalizer
        );
    }

    public function testDenormalize()
    {
        $budget = (new BudgetDenormalizer)->denormalize([
            'identity' => $identity = (string) Uuid::uuid4(),
            'name' => 'foo',
            'amount' => 42,
            'categories' => [$cat = (string) Uuid::uuid4()],
        ]);

        $this->assertInstanceOf(Budget::class, $budget);
        $this->assertSame($identity, (string) $budget->identity());
        $this->assertSame('foo', $budget->name());
        $this->assertSame(42, $budget->amount()->value());
        $this->assertSame($cat, (string) $budget->categories()->current());
        $this->assertCount(0, $budget->recordedEvents());
    }
}
