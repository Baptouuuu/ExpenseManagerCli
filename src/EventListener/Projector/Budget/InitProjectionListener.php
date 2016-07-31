<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\Budget;

use ExpenseManager\Event\MonthReportWasCreated;
use Innmind\Filesystem\{
    AdapterInterface,
    File,
    Stream\StringStream
};

final class InitProjectionListener
{
    private $filesystem;

    public function __construct(AdapterInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function __invoke(MonthReportWasCreated $event)
    {
        $this->filesystem->add(
            new File($event->date()->format('Y-m')),
            new StringStream('[]'."\n")
        );
    }
}
