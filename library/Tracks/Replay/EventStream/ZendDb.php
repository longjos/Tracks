<?php
namespace Tracks\Replay\EventStream;

class ZendDb implements IEventStream
{
    /** @staticvar int */
    const BUFFER_SIZE = 100;

    /** @var Zend_Db_Select */
    private $_query;

    /** @var array */
    private $_rowBuffer = array();

    /** @var int */
    private $_cursor = 0;

    /** @var int */
    private $_offset = 0;

    /** @var int */
    private $_bufferSize;

    /**
     * Constructor
     */
    public function __construct(Zend_Db_Select $query, $bufferSize = self::BUFFER_SIZE)
    {
        assert('is_int($bufferSize)');

        $this->_query = $query;
        $this->_bufferSize = $bufferSize;
    }

    /**
     * Get the current element
     * @see Iterator::current()
     */
    public function current()
    {
        if (empty($this->_rowBuffer)) {
            $this->loadMore();
        }

        return unserialize($this->_rowBuffer[$this->_cursor]['data']);
    }

    /**
     * Get the key for the current element
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->_cursor;
    }

    /**
     * Move the cursor to the next element
     * @see Iterator::next()
     */
    public function next()
    {
        if (++$this->_cursor >= $this->_bufferSize) {
            $this->loadMore();
            $this->_cursor = 0;
        }
    }

    /**
     * Rewind the cursor to the first element
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->_cursor = 0;

        if (empty($this->_rowBuffer)) {
            $this->loadMore();
        }
    }

    /**
     * Is the current key valid?
     * @see Iterator::valid()
     */
    public function valid()
    {
        return $this->_cursor < count($this->_rowBuffer);
    }

    /**
     * Get the SQL string for the current query
     *
     * @return string
     */
    public function getQueryAsString()
    {
        return $this->_query->assemble();
    }

    /**
     * Load more rows from the database and put them in the buffer
     */
    private function loadMore()
    {
        $eventTable = new Zend_Db_Table('event');
        $this->_rowBuffer = $eventTable
            ->fetchAll(
                $this->_query
                ->limit(self::BUFFER_SIZE, $this->_offset)
            )->toArray();

        $this->_offset += self::BUFFER_SIZE;
    }
}
