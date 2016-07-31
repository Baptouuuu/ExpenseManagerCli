<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use ExpenseManager\{
    Event\MonthReport\FixedCostHasBeenApplied,
    Repository\FixedCostRepositoryInterface,
    Repository\CategoryRepositoryInterface,
    Repository\MonthReportRepositoryInterface
};
use Innmind\Filesystem\AdapterInterface;

final class FixedCostProjectionListener
{
    use AmountUpdater;

    private $filesystem;
    private $repository;
    private $categories;
    private $reports;

    public function __construct(
        AdapterInterface $filesystem,
        FixedCostRepositoryInterface $repository,
        CategoryRepositoryInterface $categories,
        MonthReportRepositoryInterface $reports
    ) {
        $this->filesystem = $filesystem;
        $this->repository = $repository;
        $this->categories = $categories;
        $this->reports = $reports;
    }

    public function __invoke(FixedCostHasBeenApplied $event)
    {
        $report = $this->reports->get($event->identity());
        $file = $this->filesystem->get((string) $report);
        $cost = $this->repository->get($event->fixedCost());
        $file = $this->decreaseBy(
            $file,
            $cost->amount()->value(),
            $this->categories->get($cost->category())
        );
        $this->filesystem->add($file);
    }
}
