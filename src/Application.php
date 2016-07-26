<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli;

use Innmind\EventBusBundle\DependencyInjection\Compiler\{
    BuildEventBusStackPass,
    RegisterListenersPass
};
use Innmind\CommandBusBundle\DependencyInjection\Compiler\{
    BuildCommandBusStackPass,
    RegisterHandlersPass
};
use Symfony\Component\{
    Console\Application as Console,
    DependencyInjection\ContainerBuilder,
    DependencyInjection\ContainerInterface,
    DependencyInjection\Loader\YamlFileLoader,
    DependencyInjection\Loader\PhpFileLoader,
    Config\FileLocator,
    Config\Loader\DelegatingLoader,
    Config\Loader\LoaderResolver
};

final class Application
{
    private $console;
    private $container;

    public function __construct(string $configPath)
    {
        $this->console = new Console('ExpenseManager');
        $this->container = $this->buildContainer($configPath);
        $this->registerCommands([
            'command.init',
            'command.category.create',
            'command.category.list',
            'command.budget.create',
            'command.budget.list',
            'command.expense.create',
            'command.expense.list',
            'command.one_off_income.create',
            'command.one_off_income.list',
            'command.fixed_cost.create',
            'command.fixed_cost.list',
            'command.income.create',
            'command.income.list',
        ]);
    }

    public function run(): int
    {
        return $this->console->run();
    }

    private function buildContainer(string $configPath): ContainerInterface
    {
        $container = new ContainerBuilder;
        $loader = new DelegatingLoader(
            new LoaderResolver([
                new YamlFileLoader(
                    $container,
                    new FileLocator($configPath)
                ),
                new PhpFileLoader(
                    $container,
                    new FileLocator($configPath)
                ),
            ])
        );
        $loader->load('services.yml');
        $container
            ->addCompilerPass(new BuildEventBusStackPass)
            ->addCompilerPass(new RegisterListenersPass)
            ->addCompilerPass(new BuildCommandBusStackPass)
            ->addCompilerPass(new RegisterHandlersPass);
        $container->compile();

        return $container;
    }

    private function registerCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->console->add($this->container->get($command));
        }
    }
}
