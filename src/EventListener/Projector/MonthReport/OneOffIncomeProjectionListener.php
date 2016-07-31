<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use ExpenseManager\Event\OneOffIncomeWasCreated;
use Innmind\Filesystem\AdapterInterface;

final class OneOffIncomeProjectionListener
{
    use AmountUpdater;

    private $filesystem;

    public function __construct(AdapterInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function __invoke(OneOffIncomeWasCreated $event)
    {
        $file = $this->filesystem->get(
            $event->date()->format('Y-m')
        );
        $file = $this->increaseBy($file, $event->amount()->value());
        $this->filesystem->add($file);
    }
}
