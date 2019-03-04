<?php
namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProgramYearStewardInterface;
use App\Traits\StewardedEntity;
use Mockery as m;
use App\Tests\TestCase;

/**
 * @coversDefaultClass \App\Traits\StewardedEntity
 */

class StewardedEntityTest extends TestCase
{
    /**
     * @var StewardedEntity
     */
    private $traitObject;
    public function setUp()
    {
        $traitName = StewardedEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown() : void
    {
        unset($this->object);
    }

    /**
     * @covers ::setStewards
     */
    public function testSetStewards()
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(ProgramYearStewardInterface::class));
        $collection->add(m::mock(ProgramYearStewardInterface::class));
        $collection->add(m::mock(ProgramYearStewardInterface::class));

        $this->traitObject->setStewards($collection);
        $this->assertEquals($collection, $this->traitObject->getStewards());
    }

    /**
     * @covers ::removeSteward
     */
    public function testRemoveSteward()
    {
        $collection = new ArrayCollection();
        $one = m::mock(ProgramYearStewardInterface::class);
        $two = m::mock(ProgramYearStewardInterface::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setStewards($collection);
        $this->traitObject->removeSteward($one);
        $stewards = $this->traitObject->getStewards();
        $this->assertEquals(1, $stewards->count());
        $this->assertEquals($two, $stewards->first());
    }
}
