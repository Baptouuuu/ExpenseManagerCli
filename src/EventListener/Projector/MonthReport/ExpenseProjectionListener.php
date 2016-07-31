<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use ExpenseManager\Event\ExpenseWasCreated;
use Innmind\Filesystem\AdapterInterface;

final class ExpenseProjectionListener
{
    use AmountUpdater;

    private $filesystem;

    public function __construct(AdapterInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function __invoke(ExpenseWasCreated $event)
    {
        $file = $this->filesystem->get(
            $event->date()->format('Y-m')
        );
        $file = $this->decreaseBy($file, $event->amount()->value());
        $this->filesystem->add($file);
    }
}
