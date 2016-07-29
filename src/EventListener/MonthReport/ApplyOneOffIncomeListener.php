<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\MonthReport;

use ExpenseManager\{
    Cli\Entity\MonthReport\Identity,
    Command\MonthReport\ApplyOneOffIncome,
    Event\OneOffIncomeWasCreated
};
use Innmind\CommandBus\CommandBusInterface;

final class ApplyOneOffIncomeListener
{
    private $bus;

    public function __construct(CommandBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(OneOffIncomeWasCreated $event)
    {
        $this->bus->handle(
            new ApplyOneOffIncome(
                new Identity((new \DateTime)->format('Y-m')),
                $event->identity()
            )
        );
    }
}
