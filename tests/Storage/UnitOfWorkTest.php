<?php
declare(strict_types = 1);

namespace Tests\ExpenseManagerCli\Storage;

use ExpenseManagerCli\Storage\UnitOfWork;
use ExpenseManager\Entity\Category;
use Innmind\Immutable\Set;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $uow = new UnitOfWork(
            (new Set('string'))->add('stdClass')
        );

        $this->assertTrue($uow->handles('stdClass'));
        $this->assertFalse($uow->handles(Category::class));
        $entity = new \stdClass;
        $this->assertFalse($uow->has('stdClass', 'foo'));
        $this->assertSame($uow, $uow->add('foo', $entity));
        $this->assertTrue($uow->has('stdClass', 'foo'));
        $this->assertSame($entity, $uow->get('stdClass', 'foo'));
        $this->assertCount(1, $uow->all());
        $this->assertSame($entity, $uow->all()->get('stdClass#foo'));
        $this->assertSame($uow, $uow->remove('stdClass', 'foo'));
        $this->assertFalse($uow->has('stdClass', 'foo'));
    }

    /**
     * @expectedException ExpenseManagerCli\Exception\InvalidArgumentException
     */
    public function testThrowWhenAddingAnEntityNotHandled()
    {
        (new UnitOfWork(new Set('string')))->add('foo', new \stdClass);
    }
}
