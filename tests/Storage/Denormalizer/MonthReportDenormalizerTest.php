<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage\Denormalizer;

use ExpenseManager\{
    Cli\Storage\Denormalizer\MonthReportDenormalizer,
    Cli\Storage\DenormalizerInterface,
    Cli\Entity\Income\Identity as Income,
    Cli\Entity\FixedCost\Identity as FixedCost,
    Entity\MonthReport
};
use Ramsey\Uuid\Uuid;

class MonthReportDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            DenormalizerInterface::class,
            new MonthReportDenormalizer
        );
    }

    public function testDenormalize()
    {
        $report = (new MonthReportDenormalizer)->denormalize([
            'identity' => '2016-07',
            'date' => '2016-07',
            'amount' => 42,
            'appliedIncomes' => [$income = (string) Uuid::uuid4()],
            'appliedFixedCosts' => [$cost = (string) Uuid::uuid4()],
        ]);

        $this->assertInstanceOf(MonthReport::class, $report);
        $this->assertSame('2016-07', (string) $report->identity());
        $this->assertSame('2016-07', (string) $report);
        $this->assertSame(42, $report->amount()->value());
        $this->assertTrue($report->hasIncomeBeenApplied(new Income($income)));
        $this->assertTrue($report->hasFixedCostBeenApplied(new FixedCost($cost)));
    }
}
