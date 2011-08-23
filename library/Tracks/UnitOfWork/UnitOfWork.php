<?php
namespace Tracks\UnitOfWork;
use Tracks\EventStore\IRepository;
use Tracks\Model\AggregateRoot;

class UnitOfWork implements IUnitOfWork
{
    private $_repository;
    private $_registeredSaves = array();

    public function __construct(IRepository $repository)
    {
        $this->_repository = $repository;
    }

    public function registerSave(AggregateRoot $aggregateRoot)
    {
        $this->_registeredSaves[] = $aggregateRoot;
    }

    public function commit(ITransaction $transaction = NULL)
    {
        $transaction and $transaction->begin();

        try {
            foreach ($this->_registeredSaves as $aggregateRoot) {
                $this->_repository->save($aggregateRoot);
            }
        } catch (Exception $e) {
            $transaction and $transaction->rollback();
            throw $e;
        }

        $transaction and $transaction->commit();
    }
}
