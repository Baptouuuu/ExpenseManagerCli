<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\FixedCost;

use ExpenseManager\{
    Repository\FixedCostRepositoryInterface,
    Repository\CategoryRepositoryInterface,
    Entity\FixedCost,
    Cli\RelativeDay,
    Cli\Color
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};

final class ListCommand extends Command
{
    private $costRepository;
    private $categoryRepository;

    public function __construct(
        FixedCostRepositoryInterface $costRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->costRepository = $costRepository;
        $this->categoryRepository = $categoryRepository;
        parent::__construct('fixed-cost:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->costRepository
            ->all()
            ->reduce(
                $output,
                function(OutputInterface $output, FixedCost $cost): OutputInterface {
                    $category = $this->categoryRepository->get($cost->category());
                    $output->writeln(sprintf(
                        '<fg=green>%s</> <fg=yellow>%01.2f</> to be applied the <fg=green>%s</> in %s',
                        $cost->name(),
                        $cost->amount()->value() / 100,
                        new RelativeDay($cost->applyDay()->value()),
                        new Color(
                            (string) $category->color(),
                            $category->name()
                        )
                    ));

                    return $output;
                }
            );
    }
}
