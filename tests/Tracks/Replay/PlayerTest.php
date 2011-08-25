<?php
require_once 'PHPUnit/Framework/TestCase.php';
use Tracks\Replay\Player;

class Tracks_Replay_PlayerTest extends PHPUnit_Framework_TestCase
{
    public function testPlayWithoutTransaction()
    {
        $router = $this->getMock('Tracks\Replay\Router\IRouter');
        $router->expects($this->never())->method('null');

        $stream = $this->getMock('Tracks\Replay\EventStream\IEventStream');
        $stream->expects($this->never())->method('null');

        $player = new Player($router, $stream);
        $player->play();
    }

    public function testPlayWithTransaction()
    {
        $router = $this->getMock('Tracks\Replay\Router\IRouter');
        $router->expects($this->never())->method('null');

        $stream = $this->getMock('Tracks\Replay\EventStream\IEventStream');
        $stream->expects($this->never())->method('null');

        $transaction = $this->getMock('Tracks\UnitOfWork\ITransaction');
        $transaction->expects($this->once())->method('begin');
        $transaction->expects($this->once())->method('commit');
        $transaction->expects($this->never())->method('rollback');

        $player = new Player($router, $stream);
        $player->play($transaction);
    }
    
    public function testPlayWithTransactionThatFails()
    {
        $this->setExpectedException('Exception');

        $router = $this->getMock('Tracks\Replay\Router\IRouter');
        $router->expects($this->once())
            ->method('route')
            ->will($this->throwException(new Exception));

        $event = $this->getMockBuilder('Tracks\Event\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->never())->method('null');

        $stream = $this->getMock('Tracks\Replay\EventStream\IEventStream');
        $stream->expects($this->once())
            ->method('current')
            ->will($this->returnValue($event));
        $stream->expects($this->once())
            ->method('valid')
            ->will($this->returnValue(true));
        $stream->expects($this->once())
            ->method('rewind');

        $transaction = $this->getMock('Tracks\UnitOfWork\ITransaction');
        $transaction->expects($this->once())->method('begin');
        $transaction->expects($this->never())->method('commit');
        $transaction->expects($this->once())->method('rollback');

        $player = new Player($router, $stream);
        $player->play($transaction);
    }

    public function testDryRun()
    {
        $router = $this->getMock('Tracks\Replay\Router\IRouter');
        $router->expects($this->once())
            ->method('getHandlersRegisteredFor')
            ->will($this->returnValue(array(new stdClass)));

        $event = $this->getMockBuilder('Tracks\Event\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->never())->method('null');

        $stream = $this->getMock('Tracks\Replay\EventStream\IEventStream');
        $stream->expects($this->once())
            ->method('current')
            ->will($this->returnValue($event));
        $stream->expects($this->any())
            ->method('valid')
            ->will($this->onConsecutiveCalls(array(true, false)));
        $stream->expects($this->once())
            ->method('rewind');

        $player = new Player($router, $stream);
        $player->dryRun();
    }
}
