<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage\Normalizer;

use ExpenseManager\Cli\{
    Storage\NormalizerInterface,
    Storage\Normalizer\IncomeNormalizer,
    Entity\Income\Identity
};
use ExpenseManager\{
    Entity\Income,
    Amount,
    ApplyDay
};
use Ramsey\Uuid\Uuid;

class IncomeNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NormalizerInterface::class,
            new IncomeNormalizer
        );
    }

    public function testNormalize()
    {
        $normalizer = new IncomeNormalizer;
        $income = new Income(
            new Identity($uuid = (string) Uuid::uuid4()),
            'foo',
            new Amount(42),
            new ApplyDay(24)
        );

        $pair = $normalizer->normalize($income);
        $this->assertSame($uuid, $pair->key());
        $this->assertSame(
            [
                'identity' => $uuid,
                'name' => 'foo',
                'amount' => 42,
                'applyDay' => 24,
            ],
            $pair->value()
        );
    }

    /**
     * @expectedException ExpenseManager\Cli\Exception\InvalidArgumentException
     */
    public function testThrowWhenTryingToNormalizeWrongObject()
    {
        (new IncomeNormalizer)->normalize(new \stdClass);
    }
}
