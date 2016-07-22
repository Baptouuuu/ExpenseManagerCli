<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\CommandBus;

use ExpenseManager\Cli\Storage\{
    UnitOfWork,
    Persistence
};
use Innmind\{
    CommandBus\CommandBusInterface,
    CommandBus\Exception\InvalidArgumentException,
    EventBus\ContainsRecordedEventsInterface
};

final class FlushCommandBus implements CommandBusInterface
{
    private $commandBus;
    private $uow;
    private $persistence;

    public function __construct(
        CommandBusInterface $commandBus,
        UnitOfWork $uow,
        Persistence $persistence
    ) {
        $this->commandBus = $commandBus;
        $this->uow = $uow;
        $this->persistence = $persistence;
    }

    /**
     * {@inheritdod}
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
            ->foreach(function(string $reference, $entity) {
                if (!$entity instanceof ContainsRecordedEventsInterface) {
                    return;
                }

                $this->persistence->persist($entity);
            });
    }
}
