<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use ExpenseManager\{
    Event\MonthReport\FixedCostHasBeenApplied,
    Repository\FixedCostRepositoryInterface,
    Repository\CategoryRepositoryInterface
};
use Innmind\Filesystem\AdapterInterface;

final class FixedCostProjectionListener
{
    use AmountUpdater;

    private $filesystem;
    private $repository;
    private $categories;

    public function __construct(
        AdapterInterface $filesystem,
        FixedCostRepositoryInterface $repository,
        CategoryRepositoryInterface $categories
    ) {
        $this->filesystem = $filesystem;
        $this->repository = $repository;
        $this->categories = $categories;
    }

    public function __invoke(FixedCostHasBeenApplied $event)
    {
        $file = $this->filesystem->get(
            $event->date()->format('Y-m')
        );
        $cost = $this->repository->get($event->fixedCost());
        $file = $this->decreaseBy(
            $file,
            $cost->amount()->value(),
            $this->categories->get($cost->category())
        );
        $this->filesystem->add($file);
    }
}
