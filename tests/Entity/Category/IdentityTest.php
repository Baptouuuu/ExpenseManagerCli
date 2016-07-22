<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Entity\Category;

use ExpenseManager\Cli\Entity\Category\Identity;
use ExpenseManager\Entity\Category\IdentityInterface;
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
     * @expectedException ExpenseManager\Cli\Exception\InvalidArgumentException
     */
    public function testThrowWhenInvalidValue()
    {
        new Identity('42');
    }
}
