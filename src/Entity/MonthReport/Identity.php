<?php
declare(strict_types = 1);

namespace ExpenseManager\Cli\Entity\MonthReport;

use ExpenseManager\Cli\Exception\InvalidArgumentException;
use ExpenseManager\Entity\MonthReport\IdentityInterface;

final class Identity implements IdentityInterface
{
    private $value;

    public function __construct(string $month)
    {
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            throw new InvalidArgumentException;
        }

        $this->value = $month;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
