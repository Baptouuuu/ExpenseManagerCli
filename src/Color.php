<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli;

final class Color
{
    private static $allowed = [
        'black',
        'red',
        'green',
        'yellow',
        'blue',
        'magenta',
        'cyan',
        'white',
        'default',
    ];
    private $string;

    public function __construct(string $color, string $text)
    {
        $this->string = sprintf(
            '<fg=%s>%s</>',
            in_array($color, self::$allowed, true) ? $color : 'default',
            $text
        );
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
