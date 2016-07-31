<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command;

use Innmind\Filesystem\AdapterInterface;
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface,
    Helper\Table,
    Helper\TableCell
};

final class MonthReportCommand extends Command
{
    private $filesystem;

    public function __construct(AdapterInterface $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('month-report')
            ->setAliases(['status'])
            ->addArgument(
                'month',
                InputArgument::OPTIONAL,
                'The month to display',
                (new \DateTime)->format('Y-m')
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $month = $input->getArgument('month');
        $projection = $this->filesystem->get($month);
        $projection = json_decode((string) $projection->content(), true);

        $total = sprintf(
            '<fg=yellow>%s</>: %s',
            $month,
            $projection['formatted_amount']
        );
        $ventilation = [
            $projection['formatted_total_income'],
            $projection['formatted_total_expense'],
        ];
        $table = new Table($output);
        $table->setHeaders([
            [new TableCell($total, ['colspan' => 2])],
            $ventilation
        ]);
        $table->render();
    }
}
