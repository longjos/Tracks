<?php
namespace Tracks\UnitOfWork;
use Tracks\Model\AggregateRoot;

interface IUnitOfWork
{
    public function registerSave(AggregateRoot $aggregateRoot);
    public function commit();
}
