<?php
namespace Tracks\Replay;
use Tracks\Replay\Router\IRouter;
use Tracks\Replay\EventStream\IEventStream;
use Tracks\UnitOfWork\ITransaction;

class Player
{
    /** @var \Tracks\Replay\Router\IRouter */
    private $_router;

    /** @var \Tracks\Replay\EventStream\IEventStream */
    private $_stream;

    public function __construct(IRouter $router, IEventStream $stream)
    {
        $this->_router = $router;
        $this->_stream = $stream;
    }

    /**
     * Play the event stream and execute event handlers
     *
     * @param ITransaction $transaction
     */
    public function play(ITransaction $transaction = NULL)
    {
        $transaction and $transaction->begin();

        try {
            foreach ($this->_stream as $event) {
                $this->_router->route($event);
            }
        } catch (Exception $e) {
            $transaction and $transaction->rollback();
            throw $e;
        }

        $transaction and $transaction->commit();
    }

    /**
     * Print debug information about what would happen if current stream was played
     */
    public function dryRun()
    {
        echo $this->_stream->getDryRunInfo() . PHP_EOL . PHP_EOL;

        foreach ($this->_stream as $event) {
            $handlers = $this->_router->getHandlersRegisteredFor(get_class($event));
            echo get_class($event) .': '. $event->guid .': ('. implode(',', array_map('get_class', $handlers)) .')'. PHP_EOL;
        }

        echo PHP_EOL;
    }
}
