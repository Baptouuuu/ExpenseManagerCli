<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use Innmind\Filesystem\{
    File,
    Stream\StringStream
};

trait AmountUpdater
{
    private function increaseBy(File $file, int $amount): File
    {
        $content = json_decode((string) $file->content(), true);
        $content['raw_amount'] += $amount;
        $content['formatted_amount'] = sprintf(
            '<fg=%s>%01.2f</>',
            $content['raw_amount'] > 0 ? 'green' : 'red',
            $content['raw_amount'] / 100
        );
        $content['raw_total_income'] += $amount;
        $content['formatted_total_income'] = sprintf(
            '<fg=red>%01.2f</>',
            $content['raw_total_income'] / 100
        );

        return $file->withContent(new StringStream(
            json_encode($content)."\n"
        ));
    }

    private function decreaseBy(File $file, int $amount): File
    {
        $content = json_decode((string) $file->content(), true);
        $content['raw_amount'] -= $amount;
        $content['formatted_amount'] = sprintf(
            '<fg=%s>%01.2f</>',
            $content['raw_amount'] > 0 ? 'green' : 'red',
            $content['raw_amount'] / 100
        );
        $content['raw_total_expense'] += $amount;
        $content['formatted_total_expense'] = sprintf(
            '<fg=green>%01.2f</>',
            $content['raw_total_expense'] / 100
        );

        return $file->withContent(new StringStream(
            json_encode($content)."\n"
        ));
    }
}
