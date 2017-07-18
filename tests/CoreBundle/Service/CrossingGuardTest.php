<?php
namespace Tests\CoreBundle\Service;

use Ilios\CoreBundle\Service\CrossingGuard;
use Ilios\CoreBundle\Service\IliosFileSystem;
use Mockery as m;
use Tests\CoreBundle\TestCase;

class CrossingGuardTest extends TestCase
{
    /**
     * @covers \Ilios\CoreBundle\Service\CrossingGuard::__construct
     */
    public function testConstructor()
    {
        $fs = m::mock(IliosFileSystem::class);
        $obj = new CrossingGuard($fs);
        $this->assertTrue($obj instanceof CrossingGuard);
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CrossingGuard::isStopped
     */
    public function testNotStoppedWhenNoLock()
    {
        $fs = m::mock(IliosFileSystem::class);
        $fs->shouldReceive('hasLock')->with(CrossingGuard::GUARD)->once()->andReturn(false);
        $obj = new CrossingGuard($fs);
        $this->assertFalse($obj->isStopped());
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CrossingGuard::isStopped
     */
    public function testStoppedWhenLockFileExists()
    {
        $fs = m::mock(IliosFileSystem::class);
        $fs->shouldReceive('hasLock')->with(CrossingGuard::GUARD)->once()->andReturn(true);
        $obj = new CrossingGuard($fs);
        $this->assertTrue($obj->isStopped());
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CrossingGuard::enable
     */
    public function testEnable()
    {
        $fs = m::mock(IliosFileSystem::class);
        $fs->shouldReceive('createLock')->with(CrossingGuard::GUARD)->once();
        $fs->shouldReceive('hasLock')->with(CrossingGuard::GUARD)->once()->andReturn(true);
        $obj = new CrossingGuard($fs);
        $obj->enable();
        $this->assertTrue($obj->isStopped());
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CrossingGuard::enable
     */
    public function testDisable()
    {
        $fs = m::mock(IliosFileSystem::class);
        $fs->shouldReceive('releaseLock')->with(CrossingGuard::GUARD)->once();
        $fs->shouldReceive('hasLock')->with(CrossingGuard::GUARD)->once()->andReturn(false);
        $obj = new CrossingGuard($fs);
        $obj->disable();
        $this->assertFalse($obj->isStopped());
    }
}
