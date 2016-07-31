<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\Expense;

use ExpenseManager\{
    Repository\ExpenseRepositoryInterface,
    Repository\CategoryRepositoryInterface,
    Entity\Expense,
    Specification\Expense\After,
    Specification\Expense\Before
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputOption,
    Input\InputInterface,
    Output\OutputInterface
};

final class ListCommand extends Command
{
    private $expenseRepository;
    private $categoryRepository;

    public function __construct(
        ExpenseRepositoryInterface $expenseRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->expenseRepository = $expenseRepository;
        $this->categoryRepository = $categoryRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('expense:list')
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
            ->expenseRepository
            ->matching(
                (new After(
                    new \DateTime($input->getOption('from'))
                ))->and(new Before(
                    new \DateTime($input->getOption('to'))
                ))
            )
            ->reduce(
                $output,
                function(OutputInterface $output, Expense $expense): OutputInterface {
                    $category = $this->categoryRepository->get($expense->category());
                    $output->writeln(sprintf(
                        '[<fg=yellow>%s</>] %s %01.2f (note: %s)',
                        $expense->date()->format('Y-m-d'),
                        new Color(
                            (string) $category->color(),
                            $category->name()
                        ),
                        $expense->amount()->value() / 100,
                        $expense->note()
                    ));

                    return $output;
                }
            );
    }
}
