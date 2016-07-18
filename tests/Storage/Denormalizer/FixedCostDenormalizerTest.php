<?php
declare(strict_types = 1);

namespace Tests\ExpenseManagerCli\Storage\Denormalizer;

use ExpenseManagerCli\Storage\{
    Denormalizer\FixedCostDenormalizer,
    DenormalizerInterface
};
use ExpenseManager\Entity\FixedCost;
use Ramsey\Uuid\Uuid;

class FixedCostDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            DenormalizerInterface::class,
            new FixedCostDenormalizer
        );
    }

    public function testDenormalize()
    {
        $cost = (new FixedCostDenormalizer)->denormalize([
            'identity' => $identity = (string) Uuid::uuid4(),
            'name' => 'foo',
            'amount' => 42,
            'applyDay' => 24,
            'category' => $category = (string) Uuid::uuid4(),
        ]);

        $this->assertInstanceOf(FixedCost::class, $cost);
        $this->assertSame($identity, (string) $cost->identity());
        $this->assertSame('foo', $cost->name());
        $this->assertSame(42, $cost->amount()->value());
        $this->assertSame(24, $cost->applyDay()->value());
        $this->assertSame($category, (string) $cost->category());
        $this->assertCount(0, $cost->recordedEvents());
    }
}
