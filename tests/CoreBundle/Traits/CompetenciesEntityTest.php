<?php
namespace Tests\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\Competency;
use Ilios\CoreBundle\Traits\CompetenciesEntity;
use Mockery as m;

/**
 * @coversDefaultClass \Ilios\CoreBundle\Traits\CompetenciesEntity
 */

class CompetenciesEntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompetenciesEntity
     */
    private $traitObject;
    public function setUp()
    {
        $traitName = CompetenciesEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown()
    {
        m::close();
        unset($this->object);
    }

    /**
     * @covers ::setCompetencies
     */
    public function testSetCompetencies()
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(Competency::class));
        $collection->add(m::mock(Competency::class));
        $collection->add(m::mock(Competency::class));

        $this->traitObject->setCompetencies($collection);
        $this->assertEquals($collection, $this->traitObject->getCompetencies());
    }

    /**
     * @covers ::removeCompetency
     */
    public function testRemoveCompetency()
    {
        $collection = new ArrayCollection();
        $one = m::mock(Competency::class);
        $two = m::mock(Competency::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setCompetencies($collection);
        $this->traitObject->removeCompetency($one);
        $competencies = $this->traitObject->getCompetencies();
        $this->assertEquals(1, $competencies->count());
        $this->assertEquals($two, $competencies->first());
    }
}
