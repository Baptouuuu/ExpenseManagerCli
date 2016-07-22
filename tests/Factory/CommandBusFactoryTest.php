<?php
declare(strict_types = 1);

namespace Tests\ExpenseManager\Cli\Factory;

use ExpenseManager\Cli\Factory\CommandBusFactory;
use Innmind\CommandBus\CommandBus;

class CommandBusFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMake()
    {
        $called = false;

        $bus = CommandBusFactory::make([
            'stdClass' => function() use (&$called) {
                $called = true;
            }
        ]);

        $this->assertInstanceOf(CommandBus::class, $bus);
        $bus->handle(new \stdClass);
        $this->assertTrue($called);
    }
}
