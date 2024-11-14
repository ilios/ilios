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
    protected Term $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Term();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    /**
     * @covers \App\Entity\Term::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCourses());
        $this->assertCount(0, $this->object->getProgramYears());
        $this->assertCount(0, $this->object->getSessions());
        $this->assertCount(0, $this->object->getChildren());
        $this->assertCount(0, $this->object->getAamcResourceTypes());
    }

    public function testNotNullValidation(): void
    {
        $this->object->setTitle('test');
        $this->validateNotNulls(['vocabulary']);
        $this->object->setVocabulary(m::mock(VocabularyInterface::class));
        $this->validate(0);
    }

    public function testNotBlankValidation(): void
    {
        $this->object->setVocabulary(m::mock(VocabularyInterface::class));
        $this->validateNotBlanks(['title']);
        $this->object->setTitle('test');
        $this->validate(0);
        $this->object->setDescription('');
        $this->validate(0);
        $this->object->setDescription('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Term::setTitle
     * @covers \App\Entity\Term::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Term::setDescription
     * @covers \App\Entity\Term::getDescription
     */
    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\Term::setVocabulary
     * @covers \App\Entity\Term::getVocabulary
     */
    public function testSetVocabulary(): void
    {
        $this->entitySetTest('vocabulary', 'Vocabulary');
    }

    /**
     * @covers \App\Entity\Term::setParent
     * @covers \App\Entity\Term::getParent
     */
    public function testSetParent(): void
    {
        $this->entitySetTest('parent', 'Term');
    }

    /**
     * @covers \App\Entity\Term::addCourse
     */
    public function testAddCourse(): void
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::removeCourse
     */
    public function testRemoveCourse(): void
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeTerm');
    }

    /**
     * @covers \App\Entity\Term::getCourses
     */
    public function testGetCourses(): void
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::addProgramYear
     */
    public function testAddProgramYear(): void
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::removeProgramYear
     */
    public function testRemoveProgramYear(): void
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeTerm');
    }

    /**
     * @covers \App\Entity\Term::getProgramYears
     */
    public function testGetProgramYears(): void
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::addSession
     */
    public function testAddSession(): void
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::removeSession
     */
    public function testRemoveSession(): void
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeTerm');
    }

    /**
     * @covers \App\Entity\Term::getSessions
     */
    public function testGetSessions(): void
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::addAamcResourceType
     */
    public function testAddAamcResourceTypes(): void
    {
        $this->entityCollectionAddTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \App\Entity\Term::removeAamcResourceType
     */
    public function testRemoveAamcResourceTypes(): void
    {
        $this->entityCollectionRemoveTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \App\Entity\Term::getAamcResourceTypes
     * @covers \App\Entity\Term::setAamcResourceTypes
     */
    public function testGetAamcResourceTypes(): void
    {
        $this->entityCollectionSetTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \App\Entity\Term::addChild
     */
    public function testAddChild(): void
    {
        $this->entityCollectionAddTest('child', 'Term', 'getChildren');
    }

    /**
     * @covers \App\Entity\Term::removeChild
     */
    public function testRemoveChild(): void
    {
        $this->entityCollectionRemoveTest('child', 'Term', 'getChildren');
    }

    /**
     * @covers \App\Entity\Term::getChildren
     */
    public function testGetChildren(): void
    {
        $this->entityCollectionSetTest('child', 'Term', 'getChildren', 'setChildren');
    }

    /**
     * @covers \App\Entity\Term::setActive
     * @covers \App\Entity\Term::isActive
     */
    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getIndexableCourses
     */
    public function testGetIndexableCourses(): void
    {
        $course1 = m::mock(CourseInterface::class);
        $course1->shouldReceive('addTerm')->once()->with($this->object);
        $this->object->addCourse($course1);

        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('addTerm')->once()->with($this->object);
        $session->shouldReceive('getCourse')->once()->andReturn($course2);
        $this->object->addSession($session);

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals($rhett, [$course1, $course2]);
    }

    /**
     * @covers \App\Entity\Term::addProgramYearObjective
     */
    public function testAddProgramYearObjective(): void
    {
        $this->entityCollectionAddTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\Term::removeProgramYearObjective
     */
    public function testRemoveProgramYearObjective(): void
    {
        $this->entityCollectionRemoveTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\Term::setProgramYearObjectives
     * @covers \App\Entity\Term::getProgramYearObjectives
     */
    public function testGetProgramYearObjectives(): void
    {
        $this->entityCollectionSetTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\Term::addCourseObjective
     */
    public function testAddCourseObjective(): void
    {
        $this->entityCollectionAddTest('courseObjective', 'CourseObjective');
    }

    /**
     * @covers \App\Entity\Term::removeCourseObjective
     */
    public function testRemoveCourseObjective(): void
    {
        $this->entityCollectionRemoveTest('courseObjective', 'CourseObjective');
    }

    /**
     * @covers \App\Entity\Term::setCourseObjectives
     * @covers \App\Entity\Term::getCourseObjectives
     */
    public function testGetCourseObjectives(): void
    {
        $this->entityCollectionSetTest('courseObjective', 'CourseObjective');
    }

    /**
     * @covers \App\Entity\Term::addSessionObjective
     */
    public function testAddSessionObjective(): void
    {
        $this->entityCollectionAddTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Term::removeSessionObjective
     */
    public function testRemoveSessionObjective(): void
    {
        $this->entityCollectionRemoveTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Term::setSessionObjectives
     * @covers \App\Entity\Term::getSessionObjectives
     */
    public function testGetSessionObjectives(): void
    {
        $this->entityCollectionSetTest('sessionObjective', 'SessionObjective');
    }

    protected function getObject(): Term
    {
        return $this->object;
    }
}
