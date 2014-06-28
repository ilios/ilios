<?php
namespace Ilios\CoreBundle\Tests\Entity;


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
     * @covers Ilios\CoreBundle\Entity\Competency::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getPcrses());
        $this->assertEmpty($this->object->getProgramYears());
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Competency::getCompetencyId
     */
    public function testGetCompetencyId()
    {
        $this->basicGetTest('competencyId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->entitySetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::getOwningSchool
     */
    public function testGetOwningSchool()
    {
         $this->entityGetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::setParentCompetency
     */
    public function testSetParentCompetency()
    {
        $this->entitySetTest('parentCompetency', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::getParentCompetency
     */
    public function testGetParentCompetency()
    {
        $this->entityGetTest('parentCompetency', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::addPcrs
     */
    public function testAddPcrs()
    {
        $this->entityCollectionAddTest('pcrses', 'AamcPcrs', 'getPcrses', 'addPcrs');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::removePcrs
     */
    public function testRemovePcrs()
    {
        $this->entityCollectionRemoveTest('pcrses', 'AamcPcrs', 'getPcrses', 'addPcrs', 'removePcrs');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::getPcrses
     */
    public function testGetPcrses()
    {
        $this->entityCollectionGetTest('pcrses', 'AamcPcrs', 'getPcrses', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionGetTest('programYear', 'ProgramYear');
    }
}
