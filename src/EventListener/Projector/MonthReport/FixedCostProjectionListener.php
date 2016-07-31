<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use ExpenseManager\{
    Event\MonthReport\FixedCostHasBeenApplied,
    Repository\FixedCostRepositoryInterface
};
use Innmind\Filesystem\AdapterInterface;

final class FixedCostProjectionListener
{
    use AmountUpdater;

    private $filesystem;
    private repository;

    public function __construct(
        AdapterInterface $filesystem,
        FixedCostRepositoryInterface $repository
    ) {
        $this->filesystem = $filesystem;
        $this->repository = $repository;
    }

    public function __invoke(FixedCostHasBeenApplied $event)
    {
        $file = $this->filesystem->get(
            $event->date()->format('Y-m')
        );
        $cost = $this->repository->get($event->fixedCost());
        $file = $this->decreaseBy($file, $cost->amount()->value());
        $this->filesystem->add($file);
    }
}
