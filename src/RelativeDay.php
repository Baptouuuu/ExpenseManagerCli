<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli;

final class RelativeDay
{
    private $string;

    public function __construct(int $day)
    {
        switch ($day) {
            case 1:
                $this->string = '1st';
                break;
            case 2:
                $this->string = '2nd';
                break;
            case 3:
                $this->string = '3rd';
                break;
            default:
                $this->string = $day.'th';
                break;
        }
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
