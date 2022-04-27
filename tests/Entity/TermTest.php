<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CourseInterface;
use App\Entity\SessionInterface;
use App\Entity\Term;
use App\Entity\VocabularyInterface;
use Mockery as m;

/**
 * Tests for Entity Term
 * @group model
 */
class TermTest extends EntityBase
{
    /**
     * @var Term
     */
    protected $object;

    /**
     * Instantiate a Term object
     */
    protected function setUp(): void
    {
        $this->object = new Term();
    }

    /**
     * @covers \App\Entity\Term::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getSessions());
        $this->assertEmpty($this->object->getChildren());
        $this->assertEmpty($this->object->getAamcResourceTypes());
    }

    public function testNotBlankValidation()
    {
        $errors = $this->validate(2);
        $this->assertEquals([
            "title" => "NotBlank",
            "vocabulary" => "NotNull",
        ], $errors);

        $this->object->setVocabulary(m::mock(VocabularyInterface::class));
        $this->object->setTitle('test');
        $this->object->setDescription('');
        $this->validate(0);
        $this->object->setDescription('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Term::setTitle
     * @covers \App\Entity\Term::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Term::setDescription
     * @covers \App\Entity\Term::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\Term::setVocabulary
     * @covers \App\Entity\Term::getVocabulary
     */
    public function testSetVocabulary()
    {
        $this->entitySetTest('vocabulary', 'Vocabulary');
    }

    /**
     * @covers \App\Entity\Term::setParent
     * @covers \App\Entity\Term::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'Term');
    }

    /**
     * @covers \App\Entity\Term::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeTerm');
    }

    /**
     * @covers \App\Entity\Term::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeTerm');
    }

    /**
     * @covers \App\Entity\Term::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeTerm');
    }

    /**
     * @covers \App\Entity\Term::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::addAamcResourceType
     */
    public function testAddAamcResourceTypes()
    {
        $this->entityCollectionAddTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \App\Entity\Term::removeAamcResourceType
     */
    public function testRemoveAamcResourceTypes()
    {
        $this->entityCollectionRemoveTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \App\Entity\Term::getAamcResourceTypes
     * @covers \App\Entity\Term::setAamcResourceTypes
     */
    public function testGetAamcResourceTypes()
    {
        $this->entityCollectionSetTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \App\Entity\Term::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'Term', 'getChildren');
    }

    /**
     * @covers \App\Entity\Term::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'Term', 'getChildren');
    }

    /**
     * @covers \App\Entity\Term::getChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'Term', 'getChildren', 'setChildren');
    }

    /**
     * @covers \App\Entity\Term::setActive
     * @covers \App\Entity\Term::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getIndexableCourses
     */
    public function testGetIndexableCourses()
    {
        $course1 = m::mock(CourseInterface::class)
            ->shouldReceive('addTerm')->once()->with($this->object)->getMock();
        $this->object->addCourse($course1);

        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class)
            ->shouldReceive('addTerm')->once()->with($this->object)
            ->shouldReceive('getCourse')->once()
            ->andReturn($course2);
        $this->object->addSession($session->getMock());

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals($rhett, [$course1, $course2]);
    }

    /**
     * @covers \App\Entity\Term::addProgramYearObjective
     */
    public function testAddProgramYearObjective()
    {
        $this->entityCollectionAddTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\Term::removeProgramYearObjective
     */
    public function testRemoveProgramYearObjective()
    {
        $this->entityCollectionRemoveTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\Term::setProgramYearObjectives
     * @covers \App\Entity\Term::getProgramYearObjectives
     */
    public function testGetProgramYearObjectives()
    {
        $this->entityCollectionSetTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\Term::addCourseObjective
     */
    public function testAddCourseObjective()
    {
        $this->entityCollectionAddTest('courseObjective', 'CourseObjective');
    }

    /**
     * @covers \App\Entity\Term::removeCourseObjective
     */
    public function testRemoveCourseObjective()
    {
        $this->entityCollectionRemoveTest('courseObjective', 'CourseObjective');
    }

    /**
     * @covers \App\Entity\Term::setCourseObjectives
     * @covers \App\Entity\Term::getCourseObjectives
     */
    public function testGetCourseObjectives()
    {
        $this->entityCollectionSetTest('courseObjective', 'CourseObjective');
    }

    /**
     * @covers \App\Entity\Term::addSessionObjective
     */
    public function testAddSessionObjective()
    {
        $this->entityCollectionAddTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Term::removeSessionObjective
     */
    public function testRemoveSessionObjective()
    {
        $this->entityCollectionRemoveTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Term::setSessionObjectives
     * @covers \App\Entity\Term::getSessionObjectives
     */
    public function testGetSessionObjectives()
    {
        $this->entityCollectionSetTest('sessionObjective', 'SessionObjective');
    }
}
