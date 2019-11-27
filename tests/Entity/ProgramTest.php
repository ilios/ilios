<?php
namespace App\Tests\Entity;

use App\Entity\Program;
use Mockery as m;

/**
 * Tests for Entity Program
 * @group model
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
        $this->object->setSchool(m::mock('App\Entity\SchoolInterface'));

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

        $this->object->setSchool(m::mock('App\Entity\SchoolInterface'));

        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Program::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getCurriculumInventoryReports());
        $this->assertEmpty($this->object->getDirectors());
    }

    /**
     * @covers \App\Entity\Program::setTitle
     * @covers \App\Entity\Program::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Program::setShortTitle
     * @covers \App\Entity\Program::getShortTitle
     */
    public function testSetShortTitle()
    {
        $this->basicSetTest('shortTitle', 'string');
    }

    /**
     * @covers \App\Entity\Program::setDuration
     * @covers \App\Entity\Program::getDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers \App\Entity\Program::setPublishedAsTbd
     * @covers \App\Entity\Program::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \App\Entity\Program::setPublished
     * @covers \App\Entity\Program::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \App\Entity\Program::setSchool
     * @covers \App\Entity\Program::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\Program::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\Program::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\Program::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\Program::addCurriculumInventoryReport
     */
    public function testAddCurriculumInventoryReport()
    {
        $this->entityCollectionAddTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\Program::removeCurriculumInventoryReport
     */
    public function testRemoveCurriculumInventoryReport()
    {
        $this->entityCollectionRemoveTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\Program::getCurriculumInventoryReports
     * @covers \App\Entity\Program::setCurriculumInventoryReports
     */
    public function testGetCurriculumInventoryReports()
    {
        $this->entityCollectionSetTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\Program::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedProgram');
    }

    /**
     * @covers \App\Entity\Program::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedProgram');
    }

    /**
     * @covers \App\Entity\Program::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedProgram');
    }
}
