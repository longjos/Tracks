<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   Event
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */

namespace Tracks\Event;
use Tracks\Model\Guid;

/**
 * Domain Events with known Event Handlers class
 *
 * These events will know which IEventHandlers will be handling them.
 *
 * @category  Tracks
 * @package   Event
 * @author    Doug Hurst <dalan.hurst@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
abstract class KnownHandler
extends Base
{
    /**
     * @var array<IEventHandler> Set of Event Handlers for this Event
     */
    public $handlers = array();
}
