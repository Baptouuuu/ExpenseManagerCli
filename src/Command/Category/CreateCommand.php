<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Command\Category;

use ExpenseManagerCli\Entity\Category\Identity;
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
        $output->writeln(sprintf('<fg=%s>%s</> created', $color, $name));
    }
}
