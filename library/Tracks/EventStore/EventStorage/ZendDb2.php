<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage EventStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */

namespace Tracks\EventStore\EventStorage;
use Tracks\EventStore\IEventStore;
use Tracks\Model\Guid, Tracks\Model\Entity;


/**
 * Zend_Db based implementation of the event store.
 *
 * Requires two tables in a relational database compatible with Zend_Db. See
 * the schema directory for SQL to create the necessary tables.
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage EventStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */
class ZendDb implements IEventStore
{

    /**
     * Constructor
     *
     * @param Adapter $dbh A Zend Database Adapter
     *
     * @return null
     */
    public function __construct(\Zend\Db\Adapter\Adapter $dbh)
    {
        $this->_dbh = new \Zend\Db\Sql\Sql($dbh);
    }

    /**
     * Get all events associated with a guid
     *
     * @param Guid $guid Any GUID
     *
     * @return array
     */
    public function getAllEvents(Guid $guid)
    {
        $select = $this->_dbh->select()
            ->from('event', array('*'))
            ->where('guid', (string) $guid)
            ->order('date_created')
            ->order('id');
        $statement = $this->_dbh->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet;
        $resultSet->initialize($result);
        $events = array();
        foreach ($resultSet as $row) {
            $events[] = unserialize($row['data']);
        }

        return $events;
    }

    /**
     * Save an entity to the data store
     *
     * @param Entity $entity An Entity
     *
     * @return null
     */
    public function save(Entity $entity)
    {
        foreach ($entity->getAllEntities() as $child) {
            $this->_createEntity($child);
        }

        foreach ($entity->getAllAppliedEvents() as $event) {
            $this->_createEvent($event);
            $this->_incVersion($event->getGuid());
        }
    }

    /**
     * Get events associated with a guid, starting with a specific version number
     *
     * @param Guid $guid    An Entity's GUID
     * @param int  $version That Entity's version number
     *
     * @return array
     */
    public function getEventsFromVersion(Guid $guid, $version)
    {
        assert('is_int($version)');
        $select = $this->_dbh->select()
            ->from('event', array('*'))
            ->where('guid', (string) $guid)
            ->order('date_created')
            ->order('id')
            ->limit($version, PHP_INT_MAX);
        $statement = $this->_dbh->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet;
        $resultSet->initialize($result);

        $events = array();
        foreach ($resultSet as $row) {
            $events[] = unserialize($row['data']);
        }

        return $events;
    }

    /**
     * Get the object type of an entity
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return string
     */
    public function getType(Guid $guid)
    {
        if (is_null($row = $this->_getEntityByGuid($guid))) {
            return null;
        }

        return $row['type'];
    }

    /**
     * Create an event record
     *
     * @param \Tracks\Event\Base $event An Event
     *
     * @return null
     */
    private function _createEvent(\Tracks\Event\Base $event)
    {
        $insert = $this->_dbh->insert(
        	'event'
        );
        $insert->values(
            array(
                'guid' => $event->getGuid(),
                'data' => serialize($event)
            )
        );
        $this->_dbh->prepareStatementForSqlObject($insert)->execute();

    }

    /**
     * Create an entity record
     *
     * @param Entity $entity An Entity
     *
     * @return null
     */
    private function _createEntity(Entity $entity)
    {
        if (is_null($this->_getEntityByGuid($entity->getGuid()))) {
            $insert = $this->_dbh->insert(
            	'entity'
            );
            $insert->values(
                array(
                	'guid' => (string) $entity->getGuid(),
                	'type' => get_class($entity)
                )
            );
            $this->_dbh->prepareStatementForSqlObject($insert)->execute();
        }
    }

    /**
     * Get the entity record by guid
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return array
     */
    private function _getEntityByGuid(Guid $guid)
    {
        $select = $this->_dbh->select()
            ->from('entity');
        $select->where->equalTo('guid', (string) $guid);
        $statement = $this->_dbh->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet;
        $resultSet->initialize($result);
        if ($resultSet->count() < 1) {
            return null;
        }
        return $resultSet->current();
    }

    /**
     * Increment the version of an entity in the data store
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return null
     */
    private function _incVersion(Guid $guid)
    {
        $update = $this->_dbh->update(
        	'entity'
        );
        $update->set(
            array(
                'version' => new \Zend\Db\Sql\Expression('(version + 1)'),
                'guid' => (string) $guid
            )
        );
        $update->where->equalTo('guid', $guid);
        $statement = $this->_dbh->prepareStatementForSqlObject($update);
        $statement->execute();
    }

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    private $_dbh;
}

