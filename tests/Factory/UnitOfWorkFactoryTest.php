<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Factory;

use ExpenseManager\Cli\Factory\UnitOfWorkFactory;
use ExpenseManager\Cli\Storage\UnitOfWork;

class UnitOfWorkFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $uow = UnitOfWorkFactory::make(['stdClass']);

        $this->assertInstanceOf(UnitOfWork::class, $uow);
        $this->assertTrue($uow->handles('stdClass'));
    }
}
