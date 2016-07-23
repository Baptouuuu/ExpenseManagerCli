<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\Budget;

use ExpenseManager\{
    Repository\BudgetRepositoryInterface,
    Repository\CategoryRepositoryInterface,
    Entity\Budget,
    Entity\Category\IdentityInterface
};
use Innmind\Immutable\Set;
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};

final class ListCommand extends Command
{
    private $budgetRepository;
    private $categoryRepository;

    public function __construct(
        BudgetRepositoryInterface $budgetRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->budgetRepository = $budgetRepository;
        $this->categoryRepository = $categoryRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('budget:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->budgetRepository
            ->all()
            ->reduce(
                $output,
                function(OutputInterface $output, Budget $budget): OutputInterface {
                    $output->writeln(sprintf(
                        '%s (%s for %s)',
                        $budget->name(),
                        round($budget->amount()->value() / 100, 2),
                        $budget
                            ->categories()
                            ->reduce(
                                new Set('string'),
                                function(Set $carry, IdentityInterface $identity): Set {
                                    $category = $this->categoryRepository->get($identity);

                                    return $carry->add(sprintf(
                                        '<fg=%s>%s</>',
                                        $category->color(),
                                        $category->name()
                                    ));
                                }
                            )
                            ->join(', ')
                    ));

                    return $output;
                }
            );
    }
}
