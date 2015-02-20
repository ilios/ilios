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
        $this->assertEmpty($this->object->getAamcPcrses());
        $this->assertEmpty($this->object->getProgramYears());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::setOwningSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('School', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::setParentCompetency
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::addPcrs
     */
    public function testAddPcrs()
    {
        $this->entityCollectionAddTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'addAamcPcrs');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::removePcrs
     */
    public function testRemovePcrs()
    {
        $this->entityCollectionRemoveTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'addAamcPcrs', 'removePcrs');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::getPcrses
     */
    public function testGetPcrses()
    {
        $this->entityCollectionSetTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'setAamcPcrses');
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
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }
}
