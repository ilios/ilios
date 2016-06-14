<?php

namespace Ilios\CoreBundle\EventListener;

use Ilios\CoreBundle\Classes\LoggerQueue;

/**
 * Class LoggerQueueListener
 * @package Ilios\CoreBundle\EventListener
 */
class LoggerQueueListener
{
    /**
     * @var LoggerQueue
     */
    protected $queue;

    /**
     * LoggerQueueListener constructor.
     * @param LoggerQueue $queue
     */
    public function __construct(LoggerQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Flushes the log queue.
     */
    public function flushQueue()
    {
        $this->queue->flush();
    }
}
