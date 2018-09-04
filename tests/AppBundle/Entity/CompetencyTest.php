<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Competency;
use Mockery as m;

/**
 * Tests for Entity Competency
 */
class CompetencyTest extends EntityBase
{
    /**
     * @var Competency
     */
    protected $object;

    /**
     * Instantiate a Competency object
     */
    protected function setUp()
    {
        $this->object = new Competency;
    }

    /**
     * @covers \AppBundle\Entity\Competency::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAamcPcrses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getChildren());
        $this->assertEmpty($this->object->getObjectives());
    }

    /**
     * @covers \AppBundle\Entity\Competency::setTitle
     * @covers \AppBundle\Entity\Competency::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Competency::setSchool
     * @covers \AppBundle\Entity\Competency::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \AppBundle\Entity\Competency::setParent
     * @covers \AppBundle\Entity\Competency::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'Competency');
    }

    /**
     * @covers \AppBundle\Entity\Competency::setParent
     */
    public function testRemoveParent()
    {
        $obj = m::mock('AppBundle\Entity\Competency');
        $this->object->setParent($obj);
        $this->assertSame($obj, $this->object->getParent());
        $this->object->setParent(null);
        $this->assertNull($this->object->getParent());
    }

    /**
     * @covers \AppBundle\Entity\Competency::addAamcPcrs
     */
    public function testAddPcrs()
    {
        $this->entityCollectionAddTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'addAamcPcrs', 'addCompetency');
    }

    /**
     * @covers \AppBundle\Entity\Competency::removeAamcPcrs
     */
    public function testRemovePcrs()
    {
        $this->entityCollectionRemoveTest(
            'aamcPcrses',
            'AamcPcrs',
            'getAamcPcrses',
            'addAamcPcrs',
            'removeAamcPcrs',
            'removeCompetency'
        );
    }

    /**
     * @covers \AppBundle\Entity\Competency::getAamcPcrses
     * @covers \AppBundle\Entity\Competency::setAamcPcrses
     */
    public function testGetPcrses()
    {
        $this->entityCollectionSetTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'setAamcPcrses', 'addCompetency');
    }

    /**
     * @covers \AppBundle\Entity\Competency::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    /**
     * @covers \AppBundle\Entity\Competency::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeCompetency');
    }

    /**
     * @covers \AppBundle\Entity\Competency::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    /**
     * @covers \AppBundle\Entity\Competency::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Competency::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Competency::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Competency::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'Competency', 'getChildren');
    }

    /**
     * @covers \AppBundle\Entity\Competency::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'Competency', 'getChildren');
    }

    /**
     * @covers \AppBundle\Entity\Competency::getChildren
     * @covers \AppBundle\Entity\Competency::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'Competency', 'getChildren', 'setChildren');
    }

    /**
     * @covers \AppBundle\Entity\Competency::setActive
     * @covers \AppBundle\Entity\Competency::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }
}
