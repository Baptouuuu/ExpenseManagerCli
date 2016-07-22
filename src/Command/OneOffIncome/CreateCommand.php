<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\OneOffIncome;

use ExpenseManager\{
    Cli\Entity\OneOffIncome\Identity,
    Command\CreateOneOffIncome,
    Command\SpecifyOneOffIncomeNote
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

    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
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
