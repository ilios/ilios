<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\IlmSession;
use App\Entity\Course;
use App\Entity\CourseInterface;
use App\Entity\School;
use App\Entity\Session;
use App\Entity\SessionTypeInterface;
use Mockery as m;

/**
 * Tests for Entity Session
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\Session::class)]
class SessionTest extends EntityBase
{
    protected Session $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Session();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $this->object->setSessionType(m::mock(SessionTypeInterface::class));
        $this->object->setCourse(m::mock(CourseInterface::class));
        $this->object->setInstructionalNotes('');
        $this->object->setDescription('');
        $this->validate(0);
        $this->object->setInstructionalNotes('test');
        $this->object->setDescription('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNull = [
            'sessionType',
            'course',
        ];
        $this->validateNotNulls($notNull);

        $this->object->setSessionType(m::mock(SessionTypeInterface::class));
        $this->object->setCourse(m::mock(CourseInterface::class));

        $this->validate(0);
    }
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->assertCount(0, $this->object->getSessionObjectives());
        $this->assertCount(0, $this->object->getOfferings());
        $this->assertCount(0, $this->object->getTerms());
        $this->assertCount(0, $this->object->getSequenceBlocks());
        $this->assertCount(0, $this->object->getPrerequisites());
        $this->assertCount(0, $this->object->getAdministrators());
        $this->assertCount(0, $this->object->getStudentAdvisors());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetDescription(): void
    {
        $description = 'lorem ipsum';
        $this->object->setDescription($description);
        $this->assertEquals($description, $this->object->getDescription());
    }

    public function testSetAttireRequired(): void
    {
        $this->booleanSetTest('attireRequired');
    }

    public function testSetEquipmentRequired(): void
    {
        $this->booleanSetTest('equipmentRequired');
    }

    public function testSetSupplemental(): void
    {
        $this->booleanSetTest('supplemental');
    }

    public function testSetAttendanceRequired(): void
    {
        $this->booleanSetTest('attendanceRequired');
    }

    public function testSetPublishedAsTbd(): void
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    public function testSetPublished(): void
    {
        $this->booleanSetTest('published');
    }

    public function testSetInstructionalNotes(): void
    {
        $this->basicSetTest('instructionalNotes', 'string');
    }

    public function testSetSessionType(): void
    {
        $this->entitySetTest('sessionType', "SessionType");
    }

    public function testSetCourse(): void
    {
        $this->entitySetTest('course', "Course");
    }

    public function testSetIlmSession(): void
    {
        $obj = m::mock(IlmSession::class);
        $obj->shouldReceive('setSession')->with($this->object)->once();
        $this->object->setIlmSession($obj);
        $this->assertSame($obj, $this->object->getIlmSession());
    }

    public function testAddLearningMaterial(): void
    {
        $this->entityCollectionAddTest('learningMaterial', 'SessionLearningMaterial');
    }

    public function testRemoveLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'SessionLearningMaterial');
    }

    public function testGetLearningMaterials(): void
    {
        $this->entityCollectionSetTest('learningMaterial', 'SessionLearningMaterial');
    }

    public function testGetSchool(): void
    {
        $school = new School();
        $course = new Course();
        $session = new Session();
        $course->setSchool($school);
        $session->setCourse($course);
        $this->assertSame($school, $session->getSchool());
    }

    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    public function testSetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'Term');
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

    public function testAddMeshDescriptor(): void
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    public function testRemoveMeshDescriptor(): void
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    public function testSetMeshDescriptors(): void
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    public function testAddSequenceBlock(): void
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testRemoveSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testGetSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testAddOffering(): void
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    public function testRemoveOffering(): void
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    public function testSetOfferings(): void
    {
        $this->entityCollectionSetTest('offering', 'Offering');
    }

    public function testAddAdministrator(): void
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredSession');
    }

    public function testRemoveAdministrator(): void
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredSession');
    }

    public function testSetAdministrators(): void
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredSession');
    }

    public function testAddStudentAdvisor(): void
    {
        $this->entityCollectionAddTest('studentAdvisor', 'User', false, false, 'addStudentAdvisedSession');
    }

    public function testRemoveStudentAdvisor(): void
    {
        $this->entityCollectionRemoveTest('studentAdvisor', 'User', false, false, false, 'removeStudentAdvisedSession');
    }

    public function testSetStudentAdvisors(): void
    {
        $this->entityCollectionSetTest('studentAdvisor', 'User', false, false, 'addStudentAdvisedSession');
    }

    public function testAddExcludedSequenceBlock(): void
    {
        $this->entityCollectionAddTest('excludedSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testRemoveExcludedSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('excludedSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testGetExcludedSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('excludedSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testSetPostrequisite(): void
    {
        $this->entitySetTest('postrequisite', 'Session');
    }

    public function testAddPrerequisite(): void
    {
        $this->entityCollectionAddTest('prerequisite', 'Session', false, false, 'setPostrequisite');
    }

    public function testRemovePrerequisite(): void
    {
        $this->entityCollectionRemoveTest('prerequisite', 'Session');
    }

    public function testGetPrerequisites(): void
    {
        $this->entityCollectionSetTest('prerequisite', 'Session', false, false, 'setPostrequisite');
    }

    public function testGetIndexableCourses(): void
    {
        $course = m::mock(CourseInterface::class);
        $this->object->setCourse($course);


        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals([$course], $rhett);
    }

    protected function getObject(): Session
    {
        return $this->object;
    }
}
