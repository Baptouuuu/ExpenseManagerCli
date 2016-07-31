<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\EventListener\Projector\MonthReport;

use ExpenseManager\{
    Entity\Category,
    Cli\Color
};
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
        $content['formatted_amount'] = (string) new Color(
            $content['raw_amount'] > 0 ? 'green' : 'red',
            sprintf(
                '<fg=%s>%01.2f</>',
                $content['raw_amount'] / 100
            )
        );
        $content['raw_total_income'] += $amount;
        $content['formatted_total_income'] = sprintf(
            '<fg=green>%01.2f</>',
            $content['raw_total_income'] / 100
        );

        return $file->withContent(new StringStream(
            json_encode($content)."\n"
        ));
    }

    private function decreaseBy(File $file, int $amount, Category $category): File
    {
        $content = json_decode((string) $file->content(), true);
        $content['raw_amount'] -= $amount;
        $content['formatted_amount'] = (string) new Color(
            $content['raw_amount'] > 0 ? 'green' : 'red',
            sprintf(
                '<fg=%s>%01.2f</>',
                $content['raw_amount'] / 100
            )
        );
        $content['raw_total_expense'] += $amount;
        $content['formatted_total_expense'] = sprintf(
            '<fg=red>%01.2f</>',
            $content['raw_total_expense'] / 100
        );
        $categoryProjection = $content['categories'][(string) $category->identity()] ?? [
            'name' => (string) new Color((string) $category->color(), $category->name()),
            'amount' => 0,
            'formatted_amount' => '<fg=red>0.00</>',
        ];
        $categoryProjection['amount'] += $amount;
        $categoryProjection['formatted_amount'] = sprintf(
            '<fg=red>%01.2f</>',
            $categoryProjection['amount'] / 100
        );
        $content['categories'][(string) $category->identity()] = $categoryProjection;

        return $file->withContent(new StringStream(
            json_encode($content)."\n"
        ));
    }
}
