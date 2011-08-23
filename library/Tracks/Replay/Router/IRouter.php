<?php
namespace Tracks\Replay\Router;
use Tracks\Event;

interface IRouter
{
    public function route(Event\Base $event);
    public function addHandler($eventClass, $handler);
    public function getHandlersRegisteredFor($eventClass);
}
