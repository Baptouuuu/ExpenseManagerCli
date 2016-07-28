<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener;

use ExpenseManager\{
    Repository\MonthReportRepositoryInterface,
    Command\CreateMonthReport,
    Cli\Entity\MonthReport\Identity
};
use Innmind\CommandBus\CommandBusInterface;
use Symfony\Component\{
    EventDispatcher\EventSubscriberInterface,
    Console\ConsoleEvents,
    Console\Event\ConsoleCommandEvent
};

final class CreateMonthReportListener implements EventSubscriberInterface
{
    private $repository;
    private $bus;

    public function __construct(
        MonthReportRepositoryInterface $repository,
        CommandBusInterface $bus
    ) {
        $this->repository = $repository;
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'createReport',
        ];
    }

    public function createReport(ConsoleCommandEvent $event)
    {
        $date = (new \Datetime)->format('Y-m');

        if (
            !$event->commandShouldRun() ||
            $this->repository->has(new Identity($date))
        ) {
            return;
        }

        $this->bus->handle(
            new CreateMonthReport(
                new Identity($date),
                $date
            )
        );
    }
}
