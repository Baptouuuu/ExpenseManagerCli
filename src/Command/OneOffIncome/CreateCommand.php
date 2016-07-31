<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\OneOffIncome;

use ExpenseManager\{
    Cli\Entity\OneOffIncome\Identity,
    Cli\Entity\MonthReport\Identity as ReportIdentity,
    Command\CreateOneOffIncome,
    Command\SpecifyOneOffIncomeNote,
    Command\GenerateOldMonthReport,
    Repository\MonthReportRepositoryInterface
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
    private $repository;

    public function __construct(
        CommandBusInterface $commandBus,
        MonthReportRepositoryInterface $repository
    ) {
        $this->commandBus = $commandBus;
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('one-off-income:create')
            ->addArgument('amount', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $date = $helper->ask(
            $input,
            $output,
            new Question('When? [today] ', 'today')
        );
        $identity = new ReportIdentity((new \DateTime($date))->format('Y-m'));

        if (!$this->repository->has($identity)) {
            $this->commandBus->handle(
                new GenerateOldMonthReport(
                    $identity,
                    (string) $identity
                )
            );
        }

        $this->commandBus->handle(
            new CreateOneOffIncome(
                $identity = new Identity((string) Uuid::uuid4()),
                (int) round($input->getArgument('amount') * 100),
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
            new SpecifyOneOffIncomeNote(
                $identity,
                $note
            )
        );
    }
}
