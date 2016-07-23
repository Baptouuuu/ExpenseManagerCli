<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\Category;

use ExpenseManager\{
    Entity\Category,
    Repository\CategoryRepositoryInterface
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};

final class ListCommand extends Command
{
    private $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('category:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->repository
            ->all()
            ->reduce(
                $output,
                function(OutputInterface $output, Category $category): OutputInterface {
                    $output->writeln(sprintf(
                        '<fg=%s>%s</>',
                        $category->color(),
                        $category->name()
                    ));

                    return $output;
                }
            );
    }
}
