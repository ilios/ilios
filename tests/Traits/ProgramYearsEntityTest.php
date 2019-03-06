<?php
namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProgramYear;
use App\Traits\ProgramYearsEntity;
use Mockery as m;
use App\Tests\TestCase;

/**
 * @coversDefaultClass \App\Traits\ProgramYearsEntity
 */

class ProgramYearsEntityTest extends TestCase
{
    /**
     * @var ProgramYearsEntity
     */
    private $traitObject;
    public function setUp()
    {
        $traitName = ProgramYearsEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown() : void
    {
        unset($this->object);
    }

    /**
     * @covers ::setProgramYears
     */
    public function testSetProgramYears()
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(ProgramYear::class));
        $collection->add(m::mock(ProgramYear::class));
        $collection->add(m::mock(ProgramYear::class));

        $this->traitObject->setProgramYears($collection);
        $this->assertEquals($collection, $this->traitObject->getProgramYears());
    }

    /**
     * @covers ::removeProgramYear
     */
    public function testRemoveProgramYear()
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
}
