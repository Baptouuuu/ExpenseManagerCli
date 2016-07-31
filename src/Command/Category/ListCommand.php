<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\Category;

use ExpenseManager\{
    Entity\Category,
    Repository\CategoryRepositoryInterface,
    Cli\Color
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
        parent::__construct('category:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->repository
            ->all()
            ->reduce(
                $output,
                function(OutputInterface $output, Category $category): OutputInterface {
                    $output->writeln((string) new Color(
                        (string) $category->color(),
                        $category->name()
                    ));

                    return $output;
                }
            );
    }
}
