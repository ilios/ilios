<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Program;
use Mockery as m;

/**
 * Tests for Entity Program
 */
class ProgramTest extends EntityBase
{
    /**
     * @var Program
     */
    protected $object;

    /**
     * Instantiate a Program object
     */
    protected function setUp()
    {
        $this->object = new Program;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title',
            'duration'
        );
        $this->object->setSchool(m::mock('Ilios\CoreBundle\Entity\SchoolInterface'));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('DVc');
        $this->object->setDuration(30);
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNull = array(
            'school'
        );
        $this->object->setTitle('DVc');
        $this->object->setDuration(30);

        $this->validateNotNulls($notNull);

        $this->object->setSchool(m::mock('Ilios\CoreBundle\Entity\SchoolInterface'));

        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getCurriculumInventoryReports());
        $this->assertEmpty($this->object->getDirectors());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::setTitle
     * @covers \Ilios\CoreBundle\Entity\Program::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::setShortTitle
     * @covers \Ilios\CoreBundle\Entity\Program::getShortTitle
     */
    public function testSetShortTitle()
    {
        $this->basicSetTest('shortTitle', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::setDuration
     * @covers \Ilios\CoreBundle\Entity\Program::getDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::setPublishedAsTbd
     * @covers \Ilios\CoreBundle\Entity\Program::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::setPublished
     * @covers \Ilios\CoreBundle\Entity\Program::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::setSchool
     * @covers \Ilios\CoreBundle\Entity\Program::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::addCurriculumInventoryReport
     */
    public function testAddCurriculumInventoryReport()
    {
        $this->entityCollectionAddTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::removeCurriculumInventoryReport
     */
    public function testRemoveCurriculumInventoryReport()
    {
        $this->entityCollectionRemoveTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::getCurriculumInventoryReports
     * @covers \Ilios\CoreBundle\Entity\Program::setCurriculumInventoryReports
     */
    public function testGetCurriculumInventoryReports()
    {
        $this->entityCollectionSetTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedProgram');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedProgram');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Program::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedProgram');
    }
}
