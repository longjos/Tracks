<?php
namespace Tracks\UnitOfWork;

interface ITransaction
{
    public function begin();
    public function rollback();
    public function commit();
}
