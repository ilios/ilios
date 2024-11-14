<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProgramYear;
use App\Traits\ProgramYearsEntity;
use Mockery as m;
use App\Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Traits\ProgramYearsEntity::class)]
class ProgramYearsEntityTest extends TestCase
{
    private object $traitObject;
    public function setUp(): void
    {
        parent::setUp();
        $traitName = ProgramYearsEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testSetProgramYears(): void
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(ProgramYear::class));
        $collection->add(m::mock(ProgramYear::class));
        $collection->add(m::mock(ProgramYear::class));

        $this->traitObject->setProgramYears($collection);
        $this->assertEquals($collection, $this->traitObject->getProgramYears());
    }

    public function testRemoveProgramYear(): void
    {
        $collection = new ArrayCollection();
        $one = m::mock(ProgramYear::class);
        $two = m::mock(ProgramYear::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setProgramYears($collection);
        $this->traitObject->removeProgramYear($one);
        $programYears = $this->traitObject->getProgramYears();
        $this->assertEquals(1, $programYears->count());
        $this->assertEquals($two, $programYears->first());
    }

    public function testAddProgramYear(): void
    {
        $this->traitObject->setProgramYears(new ArrayCollection());
        $this->assertCount(0, $this->traitObject->getProgramYears());

        $one = m::mock(ProgramYear::class);
        $this->traitObject->addProgramYear($one);
        $this->assertCount(1, $this->traitObject->getProgramYears());
        $this->assertEquals($one, $this->traitObject->getProgramYears()->first());
        // duplicate prevention check
        $this->traitObject->addProgramYear($one);
        $this->assertCount(1, $this->traitObject->getProgramYears());
        $this->assertEquals($one, $this->traitObject->getProgramYears()->first());

        $two = m::mock(ProgramYear::class);
        $this->traitObject->addProgramYear($two);
        $this->assertCount(2, $this->traitObject->getProgramYears());
        $this->assertEquals($one, $this->traitObject->getProgramYears()->first());
        $this->assertEquals($two, $this->traitObject->getProgramYears()->last());
    }
}
