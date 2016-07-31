<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use ExpenseManager\{
    Event\MonthReport\IncomeHasBeenApplied,
    Repository\IncomeRepositoryInterface
};
use Innmind\Filesystem\AdapterInterface;

final class IncomeProjectionListener
{
    use AmountUpdater;

    private $filesystem;
    private $repository;

    public function __construct(
        AdapterInterface $filesystem,
        IncomeRepositoryInterface $repository
    ) {
        $this->filesystem = $filesystem;
        $this->repository = $repository;
    }

    public function __invoke(IncomeHasBeenApplied $event)
    {
        $file = $this->filesystem->get(
            $event->date()->format('Y-m')
        );
        $income = $this->repository->get($event->income());
        $file = $this->increaseBy($file, $income->amount()->value());
        $this->filesystem->add($file);
    }
}
