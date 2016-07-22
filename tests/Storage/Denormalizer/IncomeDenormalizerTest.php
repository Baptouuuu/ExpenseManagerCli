<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\Cli\Storage\{
    Denormalizer\IncomeDenormalizer,
    DenormalizerInterface
};
use ExpenseManager\Entity\Income;
use Ramsey\Uuid\Uuid;

class IncomeDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            DenormalizerInterface::class,
            new IncomeDenormalizer
        );
    }

    public function testDenormalize()
    {
        $income = (new IncomeDenormalizer)->denormalize([
            'identity' => $identity = (string) Uuid::uuid4(),
            'name' => 'foo',
            'amount' => 42,
            'applyDay' => 24,
        ]);

        $this->assertInstanceOf(Income::class, $income);
        $this->assertSame($identity, (string) $income->identity());
        $this->assertSame('foo', $income->name());
        $this->assertSame(42, $income->amount()->value());
        $this->assertSame(24, $income->applyDay()->value());
        $this->assertCount(0, $income->recordedEvents());
    }
}
