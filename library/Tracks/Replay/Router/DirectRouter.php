<?php
namespace Tracks\Replay\Router;
use Tracks\Event;
use Tracks\EventHandler\IEventHandler;

class DirectRouter implements IRouter
{
    /** @var array */
    private $_handlers = array();

    public function route(Event\Base $event)
    {
        if (isset($this->_handlers[get_class($event)])) {
            foreach ($this->_handlers[get_class($event)] as $handler) {
                if (is_string($handler)) {
                    $handler = new $handler;
                }

                $handler->handle($event);
            }
        }
    }

    public function addHandler($eventClass, $handler)
    {
        assert('is_string($eventClass)');
        assert('is_string($handler) or is_object($handler)');

        if (is_object($handler)
            && !($handler instanceof IEventHandler)
        ) {
            throw new LogicException('Event handlers must implement \Tracks\EventHandler\IEventHandler');
        }

        if (!isset($this->_handlers[$eventClass])) {
            $this->_handlers[$eventClass] = array();
        }

        $this->_handlers[$eventClass][] = $handler;

        return $this;
    }

    public function getHandlersRegisteredFor($eventClass)
    {
        assert('is_string($eventClass)');

        return (isset($this->_handlers[$eventClass]) ? $this->_handlers[$eventClass] : array());
    }
}
