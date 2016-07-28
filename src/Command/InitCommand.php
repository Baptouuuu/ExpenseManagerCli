<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Command;

use Innmind\Filesystem\{
    AdapterInterface,
    File,
    Directory,
    Stream\StringStream
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface,
    Question\ConfirmationQuestion,
    Question\Question
};

final class InitCommand extends Command
{
    private $filesystem;

    public function __construct(AdapterInterface $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct('init');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (
            $this->filesystem->has('.expense-manager') &&
            $this->filesystem->get('.expense-manager')->has('config.json')
        ) {
            $output->writeln('<fg=yellow>It seems your wallet is already initialized</>');

            return;
        }

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'Do you want to use a specific location to save your wallet? (default ~/.expense-manager/wallet) [y/N] ',
            false
        );

        if ($helper->ask($input, $output, $question)) {
            $question = new Question('Where to? (absolute path) ');
            $path = $helper->ask($input, $output, $question);

            if (empty($path)) {
                $output->writeln('<error>Please provide a path</>');

                return;
            }
        } else {
            $path = getenv('HOME').'/.expense-manager/wallet';
        }

        $this->filesystem->add(
            (new Directory('.expense-manager'))->add(
                new File(
                    'config.json',
                    new StringStream(json_encode([
                        'wallet_path' => $path,
                    ])."\n")
                )
            )
        );
    }
}
