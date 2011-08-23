<?php
namespace Tracks\Replay\EventStream;

class ZendDbBuilder
{
    /** @var Zend_Db_Select */
    private $_query;

    /** @var array */
    private $_eventClasses = array();

    /** @var array */
    private $_entities = array();

    /** @var Zend_Date */
    private $_startDate;

    /** @var Zend_Date */
    private $_endDate;

    /**
     * Start building an event stream
     *
     * @return Replay_EventStreamBuilder
     */
    public static function start()
    {
        return new self;
    }

    /**
     * Stream all events
     *
     * @return Replay_EventStreamBuilder
     */
    public function withAllEvents()
    {
        $this->_eventClasses = array();

        return $this;
    }

    /**
     * Stream a specific event class
     *
     * @param string $eventClass
     * @return Replay_EventStreamBuilder
     */
    public function withEvent($eventClass)
    {
        assert('is_string($eventClass)');

        $this->_eventClasses[] = $eventClass;

        return $this;
    }

    /**
     * Stream from all entities
     *
     * @return Replay_EventStreamBuilder
     */
    public function fromAllEntities()
    {
        $this->_entities = array();

        return $this;
    }

    /**
     * Get events that belong to an entity
     *
     * @param string $guid
     * @return Replay_EventStreamBuilder
     */
    public function fromEntity($guid)
    {
        assert('is_string($guid)');

        $this->_entities[] = $guid;

        return $this;
    }

    /**
     * Stream events for all time
     *
     * @return Replay_EventStreamBuilder
     */
    public function forAllTime()
    {
        $this->_startDate = null;
        $this->_endDate = null;

        return $this;
    }

    /**
     * Specify a date range from which to select events
     * Only one date range may be specified
     *
     * @param Zend_Date $startDate
     * @param Zend_Date $endDate
     * @return Replay_EventStreamBuilder
     */
    public function inDateRange(Zend_Date $startDate, Zend_Date $endDate)
    {
        $this->_startDate = $startDate;
        $this->_endDate = $endDate;

        return $this;
    }

    /**
     * Get the Replay_EventStream object
     *
     * @return Replay_EventStream
     */
    public function build()
    {
        $this->buildBaseQuery();
        $this->buildEventsQuery();
        $this->buildDateRangeQuery();
        $this->buildEntitiesQuery();

        return new Replay_EventStream($this->_query);
    }

    /**
     * Build the base query to load events
     */
    private function buildBaseQuery()
    {
        $eventTable = new Table_Event;
        $this->_query = $eventTable
            ->select()
            ->from('event', '*')
            ->setIntegrityCheck(false)
            ->order('date_created ASC')
            ->order('event.id');
    }

    /**
     * Build the event class filter part of the query
     */
    private function buildEventsQuery()
    {
        if (count($this->_eventClasses) > 0) {
            $this->_query->where(implode('OR', array_map(function($x) { return " data LIKE '%{$x}%' "; }, $this->_eventClasses)));
        }
    }

    /**
     * Build the entity guid filter part of the query
     */
    private function buildEntitiesQuery()
    {
        if (count($this->_entities) > 0) {
            $this->_query
                ->join('event_provider', 'event.event_provider_id = event_provider.id')
                ->where(new Zend_Db_Expr("event_provider.guid IN (". implode(',', array_map(function($x) { return "'{$x}'"; }, $this->_entities)) .")"));
        }
    }

    /**
     * Build the date range filter part of the query
     */
    private function buildDateRangeQuery()
    {
        if ($this->_startDate) {
            $this->_query->where('date_created >= ?', $this->_startDate->toString('Y-m-d'));
        }

        if ($this->_endDate) {
            $this->_query->where('date_created <= ?', $this->_endDate->toString('Y-m-d'));
        }
    }
}
