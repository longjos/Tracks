<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   Model
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */

namespace Tracks\Model;

/**
 * Interface for Entities which can be upgraded
 *
 * @category  Tracks
 * @package   Model
 * @author    Doug Hurst <dalan.hurst@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
interface IUpgradeable
{
    /**
     * Returns true if all upgrades have been performed
     *
     * @return boolean
     */
    public function isUpgraded();

    /**
     * Perfom actions necessary to upgrade the domain model
     *
     * @return null
     */
    public function upgradeModel();
}
