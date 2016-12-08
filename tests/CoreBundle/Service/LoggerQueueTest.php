<?php
namespace Tests\CoreBundle\Service;

use Ilios\CoreBundle\Service\LoggerQueue;
use Ilios\CoreBundle\Entity\School;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

/**
 * Class LoggerQueueTest
 * @package Tests\CoreBundle\\Classes
 */
class LoggerQueueTest extends TestCase
{

    public function tearDown()
    {
        m::close();
    }

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
    }
}
