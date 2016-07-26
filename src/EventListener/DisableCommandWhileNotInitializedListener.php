<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener;

use Innmind\Filesystem\AdapterInterface;
use Symfony\Component\{
    EventDispatcher\EventSubscriberInterface,
    Console\ConsoleEvents,
    Console\Event\ConsoleCommandEvent
};

final class DisableCommandWhileNotInitializedListener implements EventSubscriberInterface
{
    private $filesystem;

    public function __construct(AdapterInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'disable',
        ];
    }

    public function disable(ConsoleCommandEvent $event)
    {
        if (
            $this->filesystem->has('.expense-manager') &&
            $this->filesystem->get('.expense-manager')->has('config.json')
        ) {
            return;
        }

        $event->disableCommand();
        $event
            ->getOutput()
            ->writeln('Please run the <fg=green>init</> command before using the app');
    }
}
