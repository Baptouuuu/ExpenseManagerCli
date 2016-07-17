<?php
declare(strict_types = 1);

namespace Tests\ExpenseManagerCli\Entity\OneOffIncome;

use ExpenseManagerCli\Entity\OneOffIncome\Identity;
use ExpenseManager\Entity\OneOffIncome\IdentityInterface;
use Ramsey\Uuid\Uuid;

class IdentityTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $identity = new Identity(
            $uuid = (string) Uuid::uuid4()
        );

        $this->assertInstanceOf(IdentityInterface::class, $identity);
        $this->assertSame($uuid, (string) $identity);
    }

    /**
     * @expectedException ExpenseManagerCli\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidValue()
    {
        new Identity('42');
    }
}
