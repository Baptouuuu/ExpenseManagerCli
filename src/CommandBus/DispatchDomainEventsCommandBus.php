<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\CommandBus;

use ExpenseManager\Cli\Storage\UnitOfWork;
use Innmind\CommandBus\{
    CommandBusInterface,
    Exception\InvalidArgumentException
};
use Innmind\EventBus\{
    EventBusInterface,
    ContainsRecordedEventsInterface
};

final class DispatchDomainEventsCommandBus implements CommandBusInterface
{
    private $commandBus;
    private $uow;
    private $eventBus;

    public function __construct(
        CommandBusInterface $commandBus,
        UnitOfWork $uow,
        EventBusInterface $eventBus
    ) {
        $this->commandBus = $commandBus;
        $this->uow = $uow;
        $this->eventBus = $eventBus;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        if (!is_object($command)) {
            throw new InvalidArgumentException;
        }

        $this->commandBus->handle($command);
        $this
            ->uow
            ->all()
            ->foreach(function(string $id, $entity) {
                if (!$entity instanceof ContainsRecordedEventsInterface) {
                    return;
                }

                $entity
                    ->recordedEvents()
                    ->foreach(function($event) {
                        $this->eventBus->dispatch($event);
                    });
            });
    }
}
