<?php
namespace Tracks\Replay;

interface IEventStream extends \Iterator
{
    public function getDryRunInfo();
}
