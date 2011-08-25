<?php
namespace Tracks\Replay\EventStream;

interface IEventStream extends \Iterator
{
    public function getDryRunInfo();
}
