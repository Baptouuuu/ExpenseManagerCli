<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\MonthReport;

use ExpenseManager\{
    Repository\FixedCostRepositoryInterface,
    Entity\FixedCost,
    Command\MonthReport\ApplyFixedCost,
    Event\FixedCostWasCreated,
    Cli\Entity\MonthReport\Identity
};
use Innmind\CommandBus\CommandBusInterface;
use Symfony\Component\{
    EventDispatcher\EventSubscriberInterface,
    Console\ConsoleEvents,
    Console\Event\ConsoleCommandEvent
};

final class ApplyFixedCostListener implements EventSubscriberInterface
{
    private $bus;
    private $repository;

    public function __construct(
        CommandBusInterface $bus,
        FixedCostRepositoryInterface $repository
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
            ConsoleEvents::COMMAND => [['applyFixedCosts', 50]],
        ];
    }

    public function __invoke(FixedCostWasCreated $event)
    {
        if ($event->applyDay()->value() !== (int) (new \DateTime)->format('j')) {
            return;
        }

        $this->bus->handle(
            new ApplyFixedCost(
                new Identity((new \DateTime)->format('Y-m')),
                $event->identity()
            )
        );
    }

    public function applyFixedCosts(ConsoleCommandEvent $event)
    {
        if (!$event->commandShouldRun()) {
            return;
        }

        $this
            ->repository
            ->all()
            ->filter(function(FixedCost $cost): bool {
                return $cost->applyDay()->value() === (int) (new \DateTime)->format('j');
            })
            ->foreach(function(FixedCost $cost) {
                $this->bus->handle(
                    new ApplyFixedCost(
                        new Identity((new \DateTime)->format('Y-m')),
                        $cost->identity()
                    )
                );
            });
    }
}
