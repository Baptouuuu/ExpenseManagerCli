<?php
declare(strict_types = 1);

namespace Tests\ExpenseManagerCli\Factory;

use ExpenseManagerCli\Factory\UnitOfWorkFactory;
use ExpenseManagerCli\Storage\UnitOfWork;

class UnitOfWorkFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $uow = UnitOfWorkFactory::make(['stdClass']);

        $this->assertInstanceOf(UnitOfWork::class, $uow);
        $this->assertTrue($uow->handles('stdClass'));
    }
}
