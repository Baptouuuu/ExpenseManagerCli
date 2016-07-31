<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

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
            new File(
                (string) $event->date()->format('Y-m'),
                new StringStream(
                    json_encode([
                        'raw_amount' => 0,
                        'formatted_amount' => '<fg=green>0.00</>',
                        'categories' => [],
                        'raw_total_income' => 0,
                        'formatted_total_income' => '<fg=green>0.00</>',
                        'raw_total_expense' => 0,
                        'formatted_total_expense' => '<fg=red>0.00</>',
                        'categories' => [],
                    ])."\n"
                )
            )
        );
    }
}
