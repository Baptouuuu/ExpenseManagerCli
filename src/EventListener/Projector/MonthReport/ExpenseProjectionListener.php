<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use ExpenseManager\{
    Event\ExpenseWasCreated,
    Repository\CategoryRepositoryInterface
};
use Innmind\Filesystem\AdapterInterface;

final class ExpenseProjectionListener
{
    use AmountUpdater;

    private $filesystem;
    private $repository;

    public function __construct(
        AdapterInterface $filesystem,
        CategoryRepositoryInterface $repository
    ) {
        $this->filesystem = $filesystem;
        $this->repository = $repository;
    }

    public function __invoke(ExpenseWasCreated $event)
    {
        $file = $this->filesystem->get(
            $event->date()->format('Y-m')
        );
        $file = $this->decreaseBy(
            $file,
            $event->amount()->value(),
            $this->repository->get($event->category())
        );
        $this->filesystem->add($file);
    }
}
