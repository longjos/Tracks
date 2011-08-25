<?php
require_once 'PHPUnit/Framework/TestCase.php';
use Tracks\UnitOfWork\UnitOfWork;

class Tracks_UnitOfWork_UnitOfWorkTest extends PHPUnit_Framework_TestCase
{
    private $unitOfWork;

    public function testRegisterSave()
    {
        $repository = $this->getMock('Tracks\EventStore\IRepository');
        $repository->expects($this->never())->method('save');
        $this->unitOfWork = new UnitOfWork($repository);

        $aggregateRoot = $this->getMock('Tracks\Model\AggregateRoot');
        $aggregateRoot->expects($this->never())->method('null');
        $this->unitOfWork->registerSave($aggregateRoot);
    }

    public function testCommitWithoutTransaction()
    {
        $repository = $this->getMock('Tracks\EventStore\IRepository');
        $repository->expects($this->once())->method('save');

        $aggregateRoot = $this->getMock('Tracks\Model\AggregateRoot');
        $aggregateRoot->expects($this->never())->method('null');

        $this->unitOfWork = new UnitOfWork($repository);
        $this->unitOfWork->registerSave($aggregateRoot);
        $this->unitOfWork->commit();
    }

    public function testCommitWithTransaction()
    {
        $repository = $this->getMock('Tracks\EventStore\IRepository');
        $repository->expects($this->once())->method('save');

        $aggregateRoot = $this->getMock('Tracks\Model\AggregateRoot');
        $aggregateRoot->expects($this->never())->method('null');

        $transaction = $this->getMock('Tracks\UnitOfWork\ITransaction');
        $transaction->expects($this->once())->method('begin');
        $transaction->expects($this->once())->method('commit');

        $this->unitOfWork = new UnitOfWork($repository);
        $this->unitOfWork->registerSave($aggregateRoot);
        $this->unitOfWork->commit($transaction);
    }

    public function testCommitWithTransactionThatFails()
    {
        $this->setExpectedException('Exception');

        $repository = $this->getMock('Tracks\EventStore\IRepository');
        $repository->expects($this->once())
            ->method('save')
            ->will($this->throwException(new Exception));

        $aggregateRoot = $this->getMock('Tracks\Model\AggregateRoot');
        $aggregateRoot->expects($this->never())->method('null');

        $transaction = $this->getMock('Tracks\UnitOfWork\ITransaction');
        $transaction->expects($this->once())->method('begin');
        $transaction->expects($this->once())->method('rollback');

        $this->unitOfWork = new UnitOfWork($repository);
        $this->unitOfWork->registerSave($aggregateRoot);
        $this->unitOfWork->commit($transaction);
    }
}

