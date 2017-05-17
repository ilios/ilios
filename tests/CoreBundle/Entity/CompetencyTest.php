<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Competency;
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
     * @covers \Ilios\CoreBundle\Entity\Competency::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAamcPcrses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getChildren());
        $this->assertEmpty($this->object->getObjectives());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::setTitle
     * @covers \Ilios\CoreBundle\Entity\Competency::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::setSchool
     * @covers \Ilios\CoreBundle\Entity\Competency::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::setParent
     * @covers \Ilios\CoreBundle\Entity\Competency::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'Competency');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::setParent
     */
    public function testRemoveParent()
    {
        $obj = m::mock('Ilios\CoreBundle\Entity\Competency');
        $this->object->setParent($obj);
        $this->assertSame($obj, $this->object->getParent());
        $this->object->setParent(null);
        $this->assertNull($this->object->getParent());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::addAamcPcrs
     */
    public function testAddPcrs()
    {
        $this->entityCollectionAddTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'addAamcPcrs', 'addCompetency');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::removeAamcPcrs
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
     * @covers \Ilios\CoreBundle\Entity\Competency::getAamcPcrses
     * @covers \Ilios\CoreBundle\Entity\Competency::setAamcPcrses
     */
    public function testGetPcrses()
    {
        $this->entityCollectionSetTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'setAamcPcrses', 'addCompetency');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeCompetency');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'Competency', 'getChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'Competency', 'getChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::getChildren
     * @covers \Ilios\CoreBundle\Entity\Competency::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'Competency', 'getChildren', 'setChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Competency::setActive
     * @covers \Ilios\CoreBundle\Entity\Competency::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }
}
