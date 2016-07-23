<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\CommandBus;

use ExpenseManager\Cli\Storage\UnitOfWork;
use Innmind\CommandBus\{
    CommandBusInterface,
    Exception\InvalidArgumentException
};
use Innmind\EventBus\ContainsRecordedEventsInterface;

final class ClearEntitiesRecordedEventsCommandBus implements CommandBusInterface
{
    private $bus;
    private $uow;

    public function __construct(
        CommandBusInterface $bus,
        UnitOfWork $uow
    ) {
        $this->bus = $bus;
        $this->uow = $uow;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        if (!is_object($command)) {
            throw new InvalidArgumentException;
        }

        $this->bus->handle($command);
        $this
            ->uow
            ->all()
            ->foreach(function(string $id, $entity) {
                if ($entity instanceof ContainsRecordedEventsInterface) {
                    $entity->clearEvents();
                }
            });
    }
}
