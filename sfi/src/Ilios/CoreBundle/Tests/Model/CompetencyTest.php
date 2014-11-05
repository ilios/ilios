<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Competency;
use Mockery as m;

/**
 * Tests for Model Competency
 */
class CompetencyTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\Competency::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getPcrses());
        $this->assertEmpty($this->object->getProgramYears());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\Competency::getCompetencyId
     */
    public function testGetCompetencyId()
    {
        $this->basicGetTest('competencyId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->modelSetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::getOwningSchool
     */
    public function testGetOwningSchool()
    {
         $this->modelGetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::setParentCompetency
     */
    public function testSetParentCompetency()
    {
        $this->modelSetTest('parentCompetency', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::getParentCompetency
     */
    public function testGetParentCompetency()
    {
        $this->modelGetTest('parentCompetency', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::addPcrs
     */
    public function testAddPcrs()
    {
        $this->modelCollectionAddTest('pcrses', 'AamcPcrs', 'getPcrses', 'addPcrs');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::removePcrs
     */
    public function testRemovePcrs()
    {
        $this->modelCollectionRemoveTest('pcrses', 'AamcPcrs', 'getPcrses', 'addPcrs', 'removePcrs');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::getPcrses
     */
    public function testGetPcrses()
    {
        $this->modelCollectionGetTest('pcrses', 'AamcPcrs', 'getPcrses', false);
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->modelCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->modelCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Competency::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->modelCollectionGetTest('programYear', 'ProgramYear');
    }
}
