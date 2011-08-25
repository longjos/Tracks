<?php
require_once 'PHPUnit/Framework/TestCase.php';
use Tracks\EventHandler\DirectRouter;

class Tracks_EventHandler_DirectRouterTest extends PHPUnit_Framework_TestCase
{
    public function testRoute()
    {
        $handler = $this->getMock('Tracks\EventHandler\IEventHandler');
        $handler->expects($this->once())->method('execute');

        $event = $this->getMockBuilder('Tracks\Event\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->never())->method('null');

        $router = new DirectRouter;
        $router->addHandler(get_class($event), $handler);
        $router->route($event);
    }

    public function testAddHandlerWithStringHandlerClass()
    {
        $router = new DirectRouter;
        $router->addHandler('stdClass', 'handler');
    }

    public function testAddHandlerWithObjectHandler()
    {
        $handler = $this->getMock('Tracks\EventHandler\IEventHandler');
        $handler->expects($this->never())->method('execute');

        $router = new DirectRouter;
        $router->addHandler('stdClass', $handler);
    }
}
