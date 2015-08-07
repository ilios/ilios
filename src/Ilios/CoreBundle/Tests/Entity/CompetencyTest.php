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
     * @covers Ilios\CoreBundle\Entity\Competency::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::setSchool
     * @covers Ilios\CoreBundle\Entity\Competency::getSchool
     */
    public function testSetSchool()
    {
        $this->softDeleteEntitySetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::setParent
     * @covers Ilios\CoreBundle\Entity\Competency::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::addAamcPcrs
     */
    public function testAddPcrs()
    {
        $this->entityCollectionAddTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'addAamcPcrs');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::getAamcPcrses
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
        $this->softDeleteEntityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Competency::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->softDeleteEntityCollectionSetTest('programYear', 'ProgramYear');
    }
}
