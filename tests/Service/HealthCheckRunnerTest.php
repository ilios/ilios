<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\HealthCheckRunner;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;
use Mockery as m;
use App\Tests\TestCase;

/**
 * @covers \App\Service\HealthCheckRunner
 */
final class HealthCheckRunnerTest extends TestCase
{
    protected HealthCheckRunner $runner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runner = new HealthCheckRunner();
    }

    protected function tearDown(): void
    {
        unset($this->runner);
        parent::tearDown();
    }

    public function testRun(): void
    {
        $result1 = new Success('that went well.');
        $result2 = new Failure('that did not go well.');
        $result3 = new Warning('achtung!');
        $result4 = new Skip('not today.');
        $mockCheck1 = m::mock(CheckInterface::class);
        $mockCheck2 = m::mock(CheckInterface::class);
        $mockCheck3 = m::mock(CheckInterface::class);
        $mockCheck4 = m::mock(CheckInterface::class);
        $mockCheck1->shouldReceive('check')->andReturn($result1);
        $mockCheck2->shouldReceive('check')->andReturn($result2);
        $mockCheck3->shouldReceive('check')->andReturn($result3);
        $mockCheck4->shouldReceive('check')->andReturn($result4);

        $data = $this->runner->run([$mockCheck1]);
        $this->assertCount(1, $data['results']);
        $this->assertEquals(get_class($mockCheck1), $data['results'][0]['check']);
        $this->assertEquals('Success', $data['results'][0]['status']);
        $this->assertEquals('that went well.', $data['results'][0]['message']);
        $this->assertEquals(HealthCheckRunner::STATUS_OK, $data['summary_status']);

        $data = $this->runner->run([$mockCheck1, $mockCheck2, $mockCheck3, $mockCheck4]);
        $this->assertCount(4, $data['results']);
        $this->assertEquals(get_class($mockCheck1), $data['results'][0]['check']);
        $this->assertEquals('Success', $data['results'][0]['status']);
        $this->assertEquals('that went well.', $data['results'][0]['message']);
        $this->assertEquals(get_class($mockCheck2), $data['results'][1]['check']);
        $this->assertEquals('Failure', $data['results'][1]['status']);
        $this->assertEquals('that did not go well.', $data['results'][1]['message']);
        $this->assertEquals(get_class($mockCheck2), $data['results'][2]['check']);
        $this->assertEquals('Warning', $data['results'][2]['status']);
        $this->assertEquals('achtung!', $data['results'][2]['message']);
        $this->assertEquals(get_class($mockCheck3), $data['results'][3]['check']);
        $this->assertEquals('Skip', $data['results'][3]['status']);
        $this->assertEquals('not today.', $data['results'][3]['message']);
        $this->assertEquals(HealthCheckRunner::STATUS_NOT_OK, $data['summary_status']);
    }
}
