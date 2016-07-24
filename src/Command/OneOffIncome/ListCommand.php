<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\OneOffIncome;

use ExpenseManager\{
    Repository\OneOffIncomeRepositoryInterface,
    Entity\OneOffIncome,
    Specification\OneOffIncome\After,
    Specification\OneOffIncome\Before
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputOption,
    Input\InputInterface,
    Output\OutputInterface
};

final class ListCommand extends Command
{
    private $repository;
    private $categoryRepository;

    public function __construct(OneOffIncomeRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('one-off-income:list')
            ->addOption(
                'from',
                'f',
                InputOption::VALUE_REQUIRED,
                'Since when to list',
                '-1 month'
            )
            ->addOption(
                'to',
                't',
                InputOption::VALUE_REQUIRED,
                'When to list up to',
                'today'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->repository
            ->matching(
                (new After(
                    new \DateTime($input->getOption('from'))
                ))->and(new Before(
                    new \DateTime($input->getOption('to'))
                ))
            )
            ->reduce(
                $output,
                function(OutputInterface $output, OneOffIncome $income): OutputInterface {
                    $output->writeln(sprintf(
                        '[<fg=yellow>%s</>] %01.2f (note: %s)',
                        $income->date()->format('Y-m-d'),
                        $income->amount()->value() / 100,
                        $income->note()
                    ));

                    return $output;
                }
            );
    }
}
