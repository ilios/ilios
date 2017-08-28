<?php
namespace Tests\CoreBundle\Service;

use Ilios\CoreBundle\Service\LoggerQueue;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;
use Tests\CoreBundle\TestCase;

/**
 * Class LoggerQueueTest
 */
class LoggerQueueTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testFlush()
    {
        $action = 'create';
        $changes = 'foo,bar';
        $school = new School();
        $school->setId(12);
        $logger = m::mock('Ilios\CoreBundle\Service\Logger')
            ->shouldReceive('log')
            ->times(1)
            ->with($action, '12', get_class($school), $changes, false)
            ->getMock();
        $queue = new LoggerQueue($logger);
        $queue->add($action, $school, $changes);
        $queue->flush();
    }

    public function testFlushEmptyQueue()
    {
        $logger = m::mock('Ilios\CoreBundle\Service\Logger');
        $queue = new LoggerQueue($logger);
        $queue->flush();
        $logger->shouldNotHaveReceived('log');
        $this->assertTrue(true);
    }
}
