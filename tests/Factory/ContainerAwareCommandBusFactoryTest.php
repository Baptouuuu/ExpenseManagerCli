<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Factory;

use ExpenseManager\Cli\{
    Factory\ContainerAwareCommandBusFactory,
    CommandBus\ContainerAwareCommandBus
};
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommandBusFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $called = false;

        $bus = ContainerAwareCommandBusFactory::make(
            $container = $this->createMock(ContainerInterface::class),
            ['stdClass' => 'some_service']
        );
        $container
            ->method('get')
            ->willReturn(function() use (&$called) {
                $called = true;
            });

        $this->assertInstanceOf(ContainerAwareCommandBus::class, $bus);
        $bus->handle(new \stdClass);
        $this->assertTrue($called);
    }
}
