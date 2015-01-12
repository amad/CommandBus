<?php

namespace SimpleBus\Command\Tests\Bus;

use SimpleBus\Command\Bus\CommandBusStack;
use SimpleBus\Command\Command;

class CommandBusStackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_lets_all_stacked_command_buses_handle_the_command()
    {
        $actualCommandBusesCalled = [];

        $stackedCommandBuses = [
            $this->mockStackedCommandBus($actualCommandBusesCalled),
            $this->mockStackedCommandBus($actualCommandBusesCalled),
            $this->mockStackedCommandBus($actualCommandBusesCalled)
        ];

        $command = $this->dummyCommand();
        $commandBusStack = new CommandBusStack($stackedCommandBuses);
        $commandBusStack->handle($command);

        $this->assertSame($stackedCommandBuses, $actualCommandBusesCalled);
    }

    /**
     * @test
     */
    public function it_works_with_no_command_buses()
    {
        $command = $this->dummyCommand();
        $commandBusStack = new CommandBusStack([]);
        $commandBusStack->handle($command);
    }

    /**
     * @test
     */
    public function it_works_with_one_command_bus()
    {
        $actualCommandBusesCalled = [];

        $stackedCommandBuses = [
            $this->mockStackedCommandBus($actualCommandBusesCalled),
        ];

        $command = $this->dummyCommand();
        $commandBusStack = new CommandBusStack($stackedCommandBuses);
        $commandBusStack->handle($command);

        $this->assertSame($stackedCommandBuses, $actualCommandBusesCalled);
    }

    private function mockStackedCommandBus(&$actualCommandBusesCalled)
    {
        $commandBus = $this->getMock('SimpleBus\Command\Bus\StackedCommandBus');

        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->will(
                $this->returnCallback(
                    function (Command $command, callable $next) use (&$actualCommandBusesCalled, $commandBus) {
                        $actualCommandBusesCalled[] = $commandBus;
                        $next($command);
                    }
                )
            );

        return $commandBus;
    }

    private function dummyCommand()
    {
        return $this->getMock('SimpleBus\Command\Command');
    }
}
