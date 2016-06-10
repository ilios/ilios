<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Service\Logger;

/**
 * FIFO queue for tracking and logging operations on entities.
 *
 * Class LoggerQueue
 * @package Ilios\CoreBundle\Classes
 */
class LoggerQueue
{
    /**
     * @var array
     */
    protected $queue = [];

    /**
     * @var Logger
     */
    protected $logger;
    
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Adds a given action, entity and changeset to the queue.
     *
     * @param string $action
     * @param object $entity
     * @param string $changes
     */
    public function add($action, $entity, $changes)
    {
        $this->queue[] = ['action' => $action, 'entity' => $entity, 'changes' => $changes];
    }

    /**
     * Flushes out the entity queue to the audit logger.
     */
    public function flush()
    {
        if (empty($this->queue)) {
            return;
        }
        try {
            while (count($this->queue)) {
                $item = array_pop($this->queue);
                $action = $item['action'];
                $entity = $item['entity'];
                $id = (string)$entity;
                $changes = $item['changes'];
                $this->logger->log($action, $id, get_class($entity), $changes, false);
            }
            $this->logger->flush(); // explicitly flush the logger.
        } catch (\Exception $e) {
            // eat this exception.
        }
    }
}
