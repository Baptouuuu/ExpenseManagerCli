<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command\Category;

use ExpenseManager\Cli\{
    Entity\Category\Identity,
    Color as CliColor
};
use ExpenseManager\{
    Command\CreateCategory,
    Color
};
use Innmind\CommandBus\CommandBusInterface;
use Symfony\Component\Console\{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface
};
use Ramsey\Uuid\Uuid;

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
            ->setName('category:create')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('color', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $color = $input->getArgument('color');

        if (!Color::choices()->contains($color)) {
            $output->writeln(sprintf(
                '<error>Invalid color (choices: %s)</>',
                Color::choices()->join(', ')
            ));

            return;
        }

        $this->commandBus->handle(
            new CreateCategory(
                new Identity((string) Uuid::uuid4()),
                $name = $input->getArgument('name'),
                $color
            )
        );
        $output->writeln(sprintf(
            '%s created',
            new CliColor(
                $color,
                $name
            )
        ));
    }
}
