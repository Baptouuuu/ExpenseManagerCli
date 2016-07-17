<?php
declare(strict_types = 1);

namespace Tests\ExpenseManagerCli\Storage\Normalizer;

use ExpenseManagerCli\{
    Storage\NormalizerInterface,
    Storage\Normalizer\ExpenseNormalizer,
    Entity\Expense\Identity,
    Entity\Category\Identity as Category
};
use ExpenseManager\{
    Entity\Expense,
    Amount
};
use Ramsey\Uuid\Uuid;

class ExpenseNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NormalizerInterface::class,
            new ExpenseNormalizer
        );
    }

    public function testNormalize()
    {
        $normalizer = new ExpenseNormalizer;
        $expense = new Expense(
            new Identity($uuid = (string) Uuid::uuid4()),
            new Amount(42),
            new Category($cat = (string) Uuid::uuid4()),
            new \DateTimeImmutable('2016-07-14')
        );
        $expense->specifyNote('foo');

        $pair = $normalizer->normalize($expense);
        $this->assertSame($uuid, $pair->key());
        $this->assertSame(
            [
                'identity' => $uuid,
                'amount' => 42,
                'category' => $cat,
                'date' => $expense->date()->format(\DateTime::ISO8601),
                'note' => 'foo',
            ],
            $pair->value()
        );
    }

    /**
     * @expectedException ExpenseManagerCli\Exception\InvalidArgumentException
     */
    public function testThrowWhenTryingToNormalizeWrongObject()
    {
        (new ExpenseNormalizer)->normalize(new \stdClass);
    }
}
