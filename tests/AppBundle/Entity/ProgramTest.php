<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Program;
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
        $this->object->setSchool(m::mock('AppBundle\Entity\SchoolInterface'));

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

        $this->object->setSchool(m::mock('AppBundle\Entity\SchoolInterface'));

        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\Program::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getCurriculumInventoryReports());
        $this->assertEmpty($this->object->getDirectors());
    }

    /**
     * @covers \AppBundle\Entity\Program::setTitle
     * @covers \AppBundle\Entity\Program::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Program::setShortTitle
     * @covers \AppBundle\Entity\Program::getShortTitle
     */
    public function testSetShortTitle()
    {
        $this->basicSetTest('shortTitle', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Program::setDuration
     * @covers \AppBundle\Entity\Program::getDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers \AppBundle\Entity\Program::setPublishedAsTbd
     * @covers \AppBundle\Entity\Program::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \AppBundle\Entity\Program::setPublished
     * @covers \AppBundle\Entity\Program::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \AppBundle\Entity\Program::setSchool
     * @covers \AppBundle\Entity\Program::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \AppBundle\Entity\Program::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \AppBundle\Entity\Program::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \AppBundle\Entity\Program::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \AppBundle\Entity\Program::addCurriculumInventoryReport
     */
    public function testAddCurriculumInventoryReport()
    {
        $this->entityCollectionAddTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \AppBundle\Entity\Program::removeCurriculumInventoryReport
     */
    public function testRemoveCurriculumInventoryReport()
    {
        $this->entityCollectionRemoveTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \AppBundle\Entity\Program::getCurriculumInventoryReports
     * @covers \AppBundle\Entity\Program::setCurriculumInventoryReports
     */
    public function testGetCurriculumInventoryReports()
    {
        $this->entityCollectionSetTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \AppBundle\Entity\Program::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedProgram');
    }

    /**
     * @covers \AppBundle\Entity\Program::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedProgram');
    }

    /**
     * @covers \AppBundle\Entity\Program::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedProgram');
    }
}
