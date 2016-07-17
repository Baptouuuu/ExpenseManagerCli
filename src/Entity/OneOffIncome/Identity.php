<?php
declare(strict_types = 1);

namespace ExpenseManagerCli\Entity\OneOffIncome;

use ExpenseManagerCli\Exception\InvalidArgumentException;
use ExpenseManager\Entity\OneOffIncome\IdentityInterface;

final class Identity implements IdentityInterface
{
    private $value;

    public function __construct(string $uuid)
    {
        if (!preg_match('/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/', $uuid)) {
            throw new InvalidArgumentException;
        }

        $this->value = $uuid;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
