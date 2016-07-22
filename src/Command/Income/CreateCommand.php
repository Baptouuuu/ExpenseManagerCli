<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\Income;

use ExpenseManager\{
    Cli\Entity\Income\Identity,
    Entity\Category,
    Command\CreateIncome
};
use Innmind\CommandBus\CommandBusInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface
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
            ->setName('income:create')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('amount', InputArgument::REQUIRED)
            ->addArgument('applyDay', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->commandBus->handle(
            new CreateIncome(
                new Identity((string) Uuid::uuid4()),
                $input->getArgument('name'),
                (int) round($input->getArgument('amount') * 100),
                (int) $input->getArgument('applyDay')
            )
        );
    }
}
