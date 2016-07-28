<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Storage\Normalizer;

use ExpenseManager\{
    Cli\Storage\Normalizer\MonthReportNormalizer,
    Cli\Storage\NormalizerInterface,
    Amount,
    ApplyDay,
    Entity\MonthReport,
    Entity\MonthReport\IdentityInterface,
    Entity\Income,
    Entity\Income\IdentityInterface as IncomeIdentityInterface,
    Entity\FixedCost,
    Entity\FixedCost\IdentityInterface as FixedCostIdentityInterface,
    Entity\Category\IdentityInterface as CategoryIdentityInterface,
    Entity\Expense,
    Entity\Expense\IdentityInterface as ExpenseIdentityInterface,
    Entity\OneOffIncome,
    Entity\OneOffIncome\IdentityInterface as OneOffIncomeIdentityInterface
};
use Innmind\Immutable\Pair;

class MonthReportNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            NormalizerInterface::class,
            new MonthReportNormalizer
        );
    }

    public function testNormalize()
    {
        $report = new MonthReport(
            $identity = $this->createMock(IdentityInterface::class),
            new \DateTimeImmutable('2016-07')
        );
        $identity
            ->method('__toString')
            ->willReturn('baz');
        $incomeIdentity = $this->createMock(IncomeIdentityInterface::class);
        $incomeIdentity
            ->method('__toString')
            ->willReturn('bar');
        $income = new Income(
            $incomeIdentity,
            'foo',
            new Amount(42),
            new ApplyDay(1)
        );
        $report->applyIncome($income);
        $costIdentity = $this->createMock(FixedCostIdentityInterface::class);
        $costIdentity
            ->method('__toString')
            ->willReturn('foo');
        $cost = new FixedCost(
            $costIdentity,
            'foo',
            new Amount(200),
            new ApplyDay(1),
            $this->createMock(CategoryIdentityInterface::class)
        );
        $report->applyFixedCost($cost);
        $report->applyExpense(
            $expense = new Expense(
                $this->createMock(ExpenseIdentityInterface::class),
                new Amount(4200),
                $this->createMock(CategoryIdentityInterface::class),
                new \DateTimeImmutable
            )
        );
        $report->applyOneOffIncome(
            $income = new OneOffIncome(
                $this->createMock(OneOffIncomeIdentityInterface::class),
                new Amount(5000),
                new \DateTimeImmutable
            )
        );

        $pair = (new MonthReportNormalizer)->normalize($report);

        $this->assertInstanceOf(Pair::class, $pair);
        $this->assertSame('baz', $pair->key());
        $this->assertSame(
            [
                'identity' => 'baz',
                'date' => '2016-07',
                'amount' => 642,
                'appliedIncomes' => ['bar'],
                'appliedFixedCosts' => ['foo'],
            ],
            $pair->value()
        );
    }

    /**
     * @expectedException ExpenseManager\Cli\Exception\InvalidArgumentException
     */
    public function testThrowWhenNormalizingWrongObject()
    {
        (new MonthReportNormalizer)->normalize(new \stdClass);
    }
}
