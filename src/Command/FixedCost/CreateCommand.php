<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\FixedCost;

use ExpenseManager\{
    Cli\Entity\FixedCost\Identity,
    Repository\CategoryRepositoryInterface,
    Entity\Category,
    Command\CreateFixedCost
};
use Innmind\CommandBus\CommandBusInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface,
    Question\ChoiceQuestion
};

final class CreateCommand extends Command
{
    private $commandBus;
    private $categoryRepository;

    public function __construct(
        CommandBusInterface $commandBus,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->commandBus = $commandBus;
        $this->categoryRepository = $categoryRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('fixed-cost:create')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('amount', InputArgument::REQUIRED)
            ->addArgument('applyDay', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $categories = $this->categoryRepository->all();
        $data = $categories->reduce(
            ['choices' => [], 'identities' => []],
            function(array $carry, Category $category): array {
                $carry['choices'][] = $name = sprintf(
                    '<fg=%s>%s</>',
                    $category->color(),
                    $category->name()
                );
                $carry['identities'][$name] = $category->identity();

                return $carry;
            }
        );
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Which category? ',
            $data['choices']
        );

        $choice = $helper->ask($input, $output, $question);
        $category = $data['identities'][$choice];

        $this->commandBus->handle(
            new CreateFixedCost(
                new Identity((string) Uuid::uuid4()),
                $input->getArgument('name'),
                (int) round($input->getArgument('amount') * 100),
                (int) $input->getArgument('applyDay'),
                $category
            )
        );
    }
}
