<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage\Normalizer;

use ExpenseManager\Cli\{
    Storage\NormalizerInterface,
    Storage\Normalizer\OneOffIncomeNormalizer,
    Entity\OneOffIncome\Identity
};
use ExpenseManager\{
    Entity\OneOffIncome,
    Amount
};
use Ramsey\Uuid\Uuid;

class OneOffIncomeNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NormalizerInterface::class,
            new OneOffIncomeNormalizer
        );
    }

    public function testNormalize()
    {
        $normalizer = new OneOffIncomeNormalizer;
        $income = new OneOffIncome(
            new Identity($uuid = (string) Uuid::uuid4()),
            new Amount(42),
            new \DateTimeImmutable('2016-07-14')
        );
        $income->specifyNote('foo');

        $pair = $normalizer->normalize($income);
        $this->assertSame($uuid, $pair->key());
        $this->assertSame(
            [
                'identity' => $uuid,
                'amount' => 42,
                'date' => $income->date()->format(\DateTime::ISO8601),
                'note' => 'foo',
            ],
            $pair->value()
        );
    }

    /**
     * @expectedException ExpenseManager\Cli\Exception\InvalidArgumentException
     */
    public function testThrowWhenTryingToNormalizeWrongObject()
    {
        (new OneOffIncomeNormalizer)->normalize(new \stdClass);
    }
}
