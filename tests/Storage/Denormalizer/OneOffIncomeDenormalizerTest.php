<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\Cli\Storage\{
    Denormalizer\OneOffIncomeDenormalizer,
    DenormalizerInterface
};
use ExpenseManager\Entity\OneOffIncome;
use Ramsey\Uuid\Uuid;

class OneOffIncomeDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            DenormalizerInterface::class,
            new OneOffIncomeDenormalizer
        );
    }

    public function testDenormalize()
    {
        $income = (new OneOffIncomeDenormalizer)->denormalize([
            'identity' => $identity = (string) Uuid::uuid4(),
            'amount' => 42,
            'date' => '2016-07-14',
            'note' => 'foo',
        ]);

        $this->assertInstanceOf(OneOffIncome::class, $income);
        $this->assertSame($identity, (string) $income->identity());
        $this->assertSame(42, $income->amount()->value());
        $this->assertSame('160714', $income->date()->format('ymd'));
        $this->assertSame('foo', $income->note());
        $this->assertCount(0, $income->recordedEvents());
    }
}
