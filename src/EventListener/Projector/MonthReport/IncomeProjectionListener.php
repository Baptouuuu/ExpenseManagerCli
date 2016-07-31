<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use ExpenseManager\{
    Event\MonthReport\IncomeHasBeenApplied,
    Repository\IncomeRepositoryInterface,
    Repository\MonthReportRepositoryInterface
};
use Innmind\Filesystem\AdapterInterface;

final class IncomeProjectionListener
{
    use AmountUpdater;

    private $filesystem;
    private $repository;
    private $reports;

    public function __construct(
        AdapterInterface $filesystem,
        IncomeRepositoryInterface $repository,
        MonthReportRepositoryInterface $reports
    ) {
        $this->filesystem = $filesystem;
        $this->repository = $repository;
        $this->reports = $reports;
    }

    public function __invoke(IncomeHasBeenApplied $event)
    {
        $report = $this->reports->get($event->identity());
        $file = $this->filesystem->get((string) $report);
        $income = $this->repository->get($event->income());
        $file = $this->increaseBy($file, $income->amount()->value());
        $this->filesystem->add($file);
    }
}
