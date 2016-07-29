<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\MonthReport;

use ExpenseManager\{
    Cli\Entity\MonthReport\Identity,
    Command\MonthReport\ApplyExpense,
    Event\ExpenseWasCreated
};
use Innmind\CommandBus\CommandBusInterface;

final class ApplyExpenseListener
{
    private $bus;

    public function __construct(CommandBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(ExpenseWasCreated $event)
    {
        $this->bus->handle(
            new ApplyExpense(
                new Identity((new \DateTime)->format('Y-m')),
                $event->identity()
            )
        );
    }
}
