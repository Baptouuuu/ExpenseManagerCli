<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command;

use ExpenseManager\{
    Repository\MonthReportRepositoryInterface,
    Cli\Entity\MonthReport\Identity
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface
};

final class MonthReportCommand extends Command
{
    private $repository;

    public function __construct(MonthReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
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
        $report = $this->repository->get(
            new Identity($input->getArgument('month'))
        );

        $color = $report->amount()->value() > 0 ? 'green' : 'red';
        $output->writeln(sprintf(
            'Status for <fg=yellow>%s</>: <fg=%s>%01.2f</>',
            $report,
            $color,
            $report->amount()->value() / 100
        ));
    }
}
