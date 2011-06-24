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
 * Routes directly to an event handler object in the same process
 *
 * @category  Tracks
 * @package   EventHandler
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */


class DirectRouter implements IEventRouter
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
        if (isset($this->_handlers[get_class($event)])) {
            foreach ($this->_handlers[get_class($event)] as $handler) {
                if (is_string($handler)) {
                    $handler = new $handler;
                }

                $handler->execute($event);
            }
        }
    }

    /**
     * Add an event handler to the routing table
     *
     * The 2nd argument may be either an instantiated object, or the name of a
     * class to instantiate. In the second case, the class should not have any
     * required parameters on it's constructor.
     *
     * @param string               $eventClass The Event classname
     * @param IEventHandler|string $handler    An EventHandler
     *
     * @return null
     */
    public function addHandler($eventClass, $handler)
    {
        assert('is_string($eventClass)');
        assert('class_exists($eventClass)');
        if (is_object($handler)
            && !($handler instanceof \Tracks\EventHandler\IEventHandler)
        ) {
            throw new LogicException('Event handlers must implement \Tracks\EventHandler\IEventHandler');
        }

        if (!isset($this->_handlers[$eventClass])) {
            $this->_handlers[$eventClass] = array();
        }

        $this->_handlers[$eventClass][] = $handler;
    }

    /** @var array */
    private $_handlers = array();
}
