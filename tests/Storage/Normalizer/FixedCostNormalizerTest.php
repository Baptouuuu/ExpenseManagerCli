<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage\Normalizer;

use ExpenseManager\Cli\{
    Storage\NormalizerInterface,
    Storage\Normalizer\FixedCostNormalizer,
    Entity\FixedCost\Identity,
    Entity\Category\Identity as Category
};
use ExpenseManager\{
    Entity\FixedCost,
    Amount,
    ApplyDay
};
use Ramsey\Uuid\Uuid;

class FixedCostNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NormalizerInterface::class,
            new FixedCostNormalizer
        );
    }

    public function testNormalize()
    {
        $normalizer = new FixedCostNormalizer;
        $cost = new FixedCost(
            new Identity($uuid = (string) Uuid::uuid4()),
            'foo',
            new Amount(42),
            new ApplyDay(24),
            new Category($cat = (string) Uuid::uuid4())
        );

        $pair = $normalizer->normalize($cost);
        $this->assertSame($uuid, $pair->key());
        $this->assertSame(
            [
                'identity' => $uuid,
                'name' => 'foo',
                'amount' => 42,
                'applyDay' => 24,
                'category' => $cat,
            ],
            $pair->value()
        );
    }

    /**
     * @expectedException ExpenseManager\Cli\Exception\InvalidArgumentException
     */
    public function testThrowWhenTryingToNormalizeWrongObject()
    {
        (new FixedCostNormalizer)->normalize(new \stdClass);
    }
}
