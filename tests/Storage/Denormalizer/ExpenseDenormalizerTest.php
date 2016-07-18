<?php
declare(strict_types = 1);

namespace Tests\ExpenseManagerCli\Storage\Denormalizer;

use ExpenseManagerCli\Storage\{
    Denormalizer\ExpenseDenormalizer,
    DenormalizerInterface
};
use ExpenseManager\Entity\Expense;
use Ramsey\Uuid\Uuid;

class ExpenseDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            DenormalizerInterface::class,
            new ExpenseDenormalizer
        );
    }

    public function testDenormalize()
    {
        $expense = (new ExpenseDenormalizer)->denormalize([
            'identity' => $identity = (string) Uuid::uuid4(),
            'amount' => 42,
            'category' => $category = (string) Uuid::uuid4(),
            'date' => '2016-07-14',
            'note' => 'foo',
        ]);

        $this->assertInstanceOf(Expense::class, $expense);
        $this->assertSame($identity, (string) $expense->identity());
        $this->assertSame(42, $expense->amount()->value());
        $this->assertSame($category, (string) $expense->category());
        $this->assertSame('160714', $expense->date()->format('ymd'));
        $this->assertSame('foo', $expense->note());
        $this->assertCount(0, $expense->recordedEvents());
    }
}
