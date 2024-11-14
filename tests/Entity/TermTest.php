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
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\Term::class)]
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

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testSetVocabulary(): void
    {
        $this->entitySetTest('vocabulary', 'Vocabulary');
    }

    public function testSetParent(): void
    {
        $this->entitySetTest('parent', 'Term');
    }

    public function testAddCourse(): void
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addTerm');
    }

    public function testRemoveCourse(): void
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeTerm');
    }

    public function testGetCourses(): void
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addTerm');
    }

    public function testAddProgramYear(): void
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    public function testRemoveProgramYear(): void
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeTerm');
    }

    public function testGetProgramYears(): void
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    public function testAddSession(): void
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addTerm');
    }

    public function testRemoveSession(): void
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeTerm');
    }

    public function testGetSessions(): void
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addTerm');
    }

    public function testAddAamcResourceTypes(): void
    {
        $this->entityCollectionAddTest('aamcResourceType', 'AamcResourceType');
    }

    public function testRemoveAamcResourceTypes(): void
    {
        $this->entityCollectionRemoveTest('aamcResourceType', 'AamcResourceType');
    }

    public function testGetAamcResourceTypes(): void
    {
        $this->entityCollectionSetTest('aamcResourceType', 'AamcResourceType');
    }

    public function testAddChild(): void
    {
        $this->entityCollectionAddTest('child', 'Term', 'getChildren');
    }

    public function testRemoveChild(): void
    {
        $this->entityCollectionRemoveTest('child', 'Term', 'getChildren');
    }

    public function testGetChildren(): void
    {
        $this->entityCollectionSetTest('child', 'Term', 'getChildren', 'setChildren');
    }

    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }

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

    public function testAddProgramYearObjective(): void
    {
        $this->entityCollectionAddTest('programYearObjective', 'ProgramYearObjective');
    }

    public function testRemoveProgramYearObjective(): void
    {
        $this->entityCollectionRemoveTest('programYearObjective', 'ProgramYearObjective');
    }

    public function testGetProgramYearObjectives(): void
    {
        $this->entityCollectionSetTest('programYearObjective', 'ProgramYearObjective');
    }

    public function testAddCourseObjective(): void
    {
        $this->entityCollectionAddTest('courseObjective', 'CourseObjective');
    }

    public function testRemoveCourseObjective(): void
    {
        $this->entityCollectionRemoveTest('courseObjective', 'CourseObjective');
    }

    public function testGetCourseObjectives(): void
    {
        $this->entityCollectionSetTest('courseObjective', 'CourseObjective');
    }

    public function testAddSessionObjective(): void
    {
        $this->entityCollectionAddTest('sessionObjective', 'SessionObjective');
    }

    public function testRemoveSessionObjective(): void
    {
        $this->entityCollectionRemoveTest('sessionObjective', 'SessionObjective');
    }

    public function testGetSessionObjectives(): void
    {
        $this->entityCollectionSetTest('sessionObjective', 'SessionObjective');
    }

    protected function getObject(): Term
    {
        return $this->object;
    }
}
