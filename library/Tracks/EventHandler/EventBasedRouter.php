<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   EventHandler
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */

namespace Tracks\EventHandler;

/**
 * Routes to an event handler object by extrapolating from the Event's namespace
 *
 * @category  Tracks
 * @package   EventHandler
 * @author    Doug Hurst <dalan.hurst@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
class EventBasedRouter implements IEventRouter
{

    /**
     * Route an event
     *
     * @param Tracks\Event\Base $event An Event
     *
     * @return null
     */
    public function route(\Tracks\Event\Base $event)
    {
        assert('$event instanceof \Tracks\Event\KnownHandler');
        foreach ($event->handlers as $handler) {
            if (is_string($handler)) {
                $handler = new $handler;
            }

            $handler->execute($event);
        }
    }

    /**
     * Add an event handler to the routing table
     *
     * @param string               $eventClass The Event classname
     * @param IEventHandler|string $handler    An EventHandler
     *
     * @return null
     * @throws DomainException
     */
    public function addHandler($eventClass, $handler)
    {
        throw new DomainException('Handlers for this router are set by the event.');
    }
}
