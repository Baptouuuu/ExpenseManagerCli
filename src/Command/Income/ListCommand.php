<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\Income;

use ExpenseManager\{
    Repository\IncomeRepositoryInterface,
    Entity\Income,
    Cli\RelativeDay
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};

final class ListCommand extends Command
{
    private $repository;

    public function __construct(IncomeRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('income:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->repository
            ->all()
            ->reduce(
                $output,
                function(OutputInterface $output, Income $income): OutputInterface {
                    $output->writeln(sprintf(
                        '<fg=green>%s</> <fg=yellow>%01.2f</> incoming the %s',
                        $income->name(),
                        $income->amount()->value() / 100,
                        new RelativeDay($income->applyDay()->value())
                    ));

                    return $output;
                }
            );
    }
}
