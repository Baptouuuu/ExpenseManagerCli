<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\Expense;

use ExpenseManager\{
    Cli\Entity\Expense\Identity,
    Cli\Entity\MonthReport\Identity as ReportIdentity,
    Repository\CategoryRepositoryInterface,
    Repository\MonthReportRepositoryInterface,
    Entity\Category,
    Command\CreateExpense,
    Command\SpecifyExpenseNote,
    Command\GenerateOldMonthReport
};
use Innmind\CommandBus\CommandBusInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface,
    Question\ChoiceQuestion,
    Question\Question
};

final class CreateCommand extends Command
{
    private $commandBus;
    private $categoryRepository;
    private $reports;

    public function __construct(
        CommandBusInterface $commandBus,
        CategoryRepositoryInterface $categoryRepository,
        MonthReportRepositoryInterface $reports
    ) {
        $this->commandBus = $commandBus;
        $this->categoryRepository = $categoryRepository;
        $this->reports = $reports;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('expense:create')
            ->addArgument('amount', InputArgument::REQUIRED);
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

        $date = $helper->ask(
            $input,
            $output,
            new Question('When? [today] ', 'today')
        );

        $reportIdentity = new ReportIdentity((new \DateTime($date))->format('Y-m'));

        if (!$this->reports->has($reportIdentity)) {
            $this->commandBus->handle(
                new GenerateOldMonthReport(
                    $reportIdentity,
                    (string) $reportIdentity
                )
            );
        }

        $this->commandBus->handle(
            new CreateExpense(
                $identity = new Identity((string) Uuid::uuid4()),
                (int) round($input->getArgument('amount') * 100),
                $category,
                $date
            )
        );

        $note = $helper->ask(
            $input,
            $output,
            new Question('A note? ', '')
        );

        if (empty($note)) {
            return;
        }

        $this->commandBus->handle(
            new SpecifyExpenseNote(
                $identity,
                $note
            )
        );
    }
}
