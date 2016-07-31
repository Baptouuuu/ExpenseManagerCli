<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\MonthReport;

use ExpenseManager\{
    Repository\IncomeRepositoryInterface,
    Entity\Income,
    Command\MonthReport\ApplyIncome,
    Event\IncomeWasCreated,
    Cli\Entity\MonthReport\Identity
};
use Innmind\CommandBus\CommandBusInterface;
use Symfony\Component\{
    EventDispatcher\EventSubscriberInterface,
    Console\ConsoleEvents,
    Console\Event\ConsoleCommandEvent
};

final class ApplyIncomeListener implements EventSubscriberInterface
{
    private $bus;
    private $repository;

    public function __construct(
        CommandBusInterface $bus,
        IncomeRepositoryInterface $repository
    ) {
        $this->bus = $bus;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => [['applyIncomes', 50]],
        ];
    }

    public function __invoke(IncomeWasCreated $event)
    {
        if ($event->applyDay()->value() !== (int) (new \DateTime)->format('j')) {
            return;
        }

        $this->bus->handle(
            new ApplyIncome(
                new Identity((new \DateTime)->format('Y-m')),
                $event->identity()
            )
        );
    }

    public function applyIncomes(ConsoleCommandEvent $event)
    {
        if (!$event->commandShouldRun()) {
            return;
        }

        $this
            ->repository
            ->all()
            ->filter(function(Income $income) {
                return $income->applyDay()->value() === (int) (new \DateTime)->format('j');
            })
            ->foreach(function(Income $income) {
                $this->bus->handle(
                    new ApplyIncome(
                        new Identity((new \DateTime)->format('Y-m')),
                        $income->identity()
                    )
                );
            });
    }
}
