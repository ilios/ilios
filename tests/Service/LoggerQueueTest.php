<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\Logger;
use App\Service\LoggerQueue;
use App\Entity\School;
use Mockery as m;
use App\Tests\TestCase;

/**
 * Class LoggerQueueTest
 */
final class LoggerQueueTest extends TestCase
{
    public function testFlush(): void
    {
        $action = 'create';
        $changes = 'foo,bar';
        $school = new School();
        $school->setId(12);
        $logger = m::mock(Logger::class);
        $logger->shouldReceive('log')
            ->times(1)
            ->with($action, '12', 'IliosTestSchoolEntity', $changes, false);
        $queue = new LoggerQueue($logger);
        $queue->add($action, $school, 'IliosTestSchoolEntity', $changes);
        $queue->flush();
    }

    public function testFlushEmptyQueue(): void
    {
        $logger = m::mock(Logger::class);
        $queue = new LoggerQueue($logger);
        $queue->flush();
        $logger->shouldNotHaveReceived('log');
    }
}
