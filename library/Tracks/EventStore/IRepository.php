<?php
namespace Tracks\EventStore;
use Tracks\Model\Guid;
use Tracks\Model\AggregateRoot;

interface IRepository
{
    public function load(Guid $guid);
    public function save(AggregateRoot $aggregateRoot);
    public function setSnapshotFrequency($numEvents);
}

