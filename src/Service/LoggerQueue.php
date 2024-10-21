<?php

declare(strict_types=1);

namespace App\Service;

use Exception;

/**
 * FIFO queue for tracking and logging operations on entities.
 *
 * Class LoggerQueue
 */
class LoggerQueue
{
    protected array $queue = [];

    public function __construct(protected Logger $logger)
    {
    }

    /**
     * Adds a given action, entity and change set to the queue.
     */
    public function add(string $action, object $entity, string $className, string $changes): void
    {
        $this->queue[] = [
            'action' => $action,
            'entity' => $entity,
            'className' => $className,
            //deleted entities lose their ID before they can be logged so we must record it here
            'id' => (string)$entity,
            'changes' => $changes,
        ];
    }

    /**
     * Flushes out the entity queue to the audit logger.
     */
    public function flush(): void
    {
        if (empty($this->queue)) {
            return;
        }
        try {
            while (count($this->queue)) {
                $item = array_pop($this->queue);
                $action = $item['action'];
                //New entities don't have an ID until this point
                $objectId = $action === 'delete' ? $item['id'] : (string)$item['entity'];
                $changes = $item['changes'];
                $this->logger->log($action, $objectId, $item['className'], $changes, false);
            }
            $this->logger->flush(); // explicitly flush the logger.
        } catch (Exception) {
            // eat this exception.
        }
    }
}
