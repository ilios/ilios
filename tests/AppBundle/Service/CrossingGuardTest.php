<?php
namespace Tests\AppBundle\Service;

use AppBundle\Service\CrossingGuard;
use AppBundle\Service\IliosFileSystem;
use Mockery as m;
use Tests\CoreBundle\TestCase;

class CrossingGuardTest extends TestCase
{
    /**
     * @covers \AppBundle\Service\CrossingGuard::__construct
     */
    public function testConstructor()
    {
        $fs = m::mock(IliosFileSystem::class);
        $obj = new CrossingGuard($fs);
        $this->assertTrue($obj instanceof CrossingGuard);
    }

    /**
     * @covers \AppBundle\Service\CrossingGuard::isStopped
     */
    public function testNotStoppedWhenNoLock()
    {
        $fs = m::mock(IliosFileSystem::class);
        $fs->shouldReceive('hasLock')->with(CrossingGuard::GUARD)->once()->andReturn(false);
        $obj = new CrossingGuard($fs);
        $this->assertFalse($obj->isStopped());
    }

    /**
     * @covers \AppBundle\Service\CrossingGuard::isStopped
     */
    public function testStoppedWhenLockFileExists()
    {
        $fs = m::mock(IliosFileSystem::class);
        $fs->shouldReceive('hasLock')->with(CrossingGuard::GUARD)->once()->andReturn(true);
        $obj = new CrossingGuard($fs);
        $this->assertTrue($obj->isStopped());
    }

    /**
     * @covers \AppBundle\Service\CrossingGuard::enable
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
     * @covers \AppBundle\Service\CrossingGuard::enable
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
