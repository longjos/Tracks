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
 * Routes to an event handler by referencing a lookup object
 *
 * Configuration should be INI-style in the format:
 *
 *     [Event1Classname]
 *     handlers[] = EventHandler1Classname
 *     handlers[] = EventHandler2Classname
 *
 *     [Event2Classname]
 *     handlers[] = EventHandler3Classname
 *     ...
 *
 * @category  Tracks
 * @package   EventHandler
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @author    Doug Hurst <doug.hurst@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
class ConfigBasedRouter implements IEventRouter
{
    /**
     * @var array An associative array of events to an array of handlers
     */
    private $_config;

    /**
     * @var array<IEventHandler> Associative array of Event Handlers
     */
    private $_handlers = array();

    /**
     * Route an event
     *
     * @param Tracks\Event\Base $event An Event
     *
     * @return null
     */
    public function route(\Tracks\Event\Base $event)
    {
        $eventClass = get_class($event);
        if (isset($this->_handlers[$eventClass])) {
            foreach ($this->_handlers[$eventClass] as $handler) {
                $handler->execute($event);
            }
        } else if (isset($this->_config[$eventClass])) {
            foreach ($this->_config[$eventClass]['handlers'] as $handlerClass) {
                $this->addHandler($eventClass, new $handlerClass());
            }
            $this->route($event);
        }
    }

    /**
     * Add an event handler to the routing table
     *
     * @param string        $eventClass The Event classname
     * @param IEventHandler $handler    An EventHandler
     *
     * @return null
     */
    public function addHandler($eventClass, $handler)
    {
        assert('is_string($eventClass)');
        assert('class_exists($eventClass)');

        if (!($handler instanceof IEventHandler)) {
            throw new \LogicException('Event handlers must implement IEventHandler');
        }

        if (!isset($this->_handlers[$eventClass])) {
            $this->_handlers[$eventClass] = array();
        }

        $this->_handlers[$eventClass][] = $handler;
    }

    /**
     * Routing Configuration Mutator
     *
     * @param string $filename The configuration filename
     *
     * @return null
     */
    public function __construct($filename)
    {
        if (file_exists($filename)) {
            $this->_config = parse_ini_file($filename, true);
        } else {
            throw new \InvalidArgumentException();
        }
    }
}
