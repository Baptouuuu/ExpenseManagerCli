<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\Budget;

use ExpenseManager\Cli\{
    Entity\Budget\Identity,
    Color
};
use ExpenseManager\{
    Entity\Category,
    Repository\CategoryRepositoryInterface,
    Command\CreateBudget
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
    private $repository;

    public function __construct(
        CommandBusInterface $commandBus,
        CategoryRepositoryInterface $repository
    ) {
        $this->commandBus = $commandBus;
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('budget:create')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('amount', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $categories = $this->repository->all();
        $data = $categories->reduce(
            ['choices' => [], 'identities' => []],
            function(array $carry, Category $category): array {
                $carry['choices'][] = $name = (string) new Color(
                    (string) $category->color(),
                    $category->name()
                );
                $carry['identities'][$name] = $category->identity();

                return $carry;
            }
        );
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Which categories should be in this budget? ',
            $data['choices']
        );
        $question->setMultiselect(true);

        $choices = $helper->ask($input, $output, $question);
        $identities = [];

        foreach ($choices as $value) {
            $identities[] = $data['identities'][$value];
        }

        $this->commandBus->handle(
            new CreateBudget(
                new Identity((string) Uuid::uuid4()),
                $input->getArgument('name'),
                (int) round($input->getArgument('amount') * 100),
                $identities
            )
        );
    }
}
