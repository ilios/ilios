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
 * @group model
 */
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
    /**
     * @covers \App\Entity\Session::__construct
     */
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

    /**
     * @covers \App\Entity\Session::setTitle
     * @covers \App\Entity\Session::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Session::setDescription
     * @covers \App\Entity\Session::getDescription
     */
    public function testSetDescription(): void
    {
        $description = 'lorem ipsum';
        $this->object->setDescription($description);
        $this->assertEquals($description, $this->object->getDescription());
    }

    /**
     * @covers \App\Entity\Session::setAttireRequired
     * @covers \App\Entity\Session::isAttireRequired
     */
    public function testSetAttireRequired(): void
    {
        $this->booleanSetTest('attireRequired');
    }

    /**
     * @covers \App\Entity\Session::setEquipmentRequired
     * @covers \App\Entity\Session::isEquipmentRequired
     */
    public function testSetEquipmentRequired(): void
    {
        $this->booleanSetTest('equipmentRequired');
    }

    /**
     * @covers \App\Entity\Session::setSupplemental
     * @covers \App\Entity\Session::isSupplemental
     */
    public function testSetSupplemental(): void
    {
        $this->booleanSetTest('supplemental');
    }

    /**
     * @covers \App\Entity\Session::setAttendanceRequired
     * @covers \App\Entity\Session::isAttendanceRequired
     */
    public function testSetAttendanceRequired(): void
    {
        $this->booleanSetTest('attendanceRequired');
    }

    /**
     * @covers \App\Entity\Session::setPublishedAsTbd
     * @covers \App\Entity\Session::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd(): void
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \App\Entity\Session::setPublished
     * @covers \App\Entity\Session::isPublished
     */
    public function testSetPublished(): void
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \App\Entity\Session::setInstructionalNotes
     * @covers \App\Entity\Session::getInstructionalNotes
     */
    public function testSetInstructionalNotes(): void
    {
        $this->basicSetTest('instructionalNotes', 'string');
    }

    /**
     * @covers \App\Entity\Session::setSessionType
     * @covers \App\Entity\Session::getSessionType
     */
    public function testSetSessionType(): void
    {
        $this->entitySetTest('sessionType', "SessionType");
    }

    /**
     * @covers \App\Entity\Session::setCourse
     * @covers \App\Entity\Session::getCourse
     */
    public function testSetCourse(): void
    {
        $this->entitySetTest('course', "Course");
    }

    /**
     * @covers \App\Entity\Session::setIlmSession
     * @covers \App\Entity\Session::getIlmSession
     */
    public function testSetIlmSession(): void
    {
        $obj = m::mock(IlmSession::class);
        $obj->shouldReceive('setSession')->with($this->object)->once();
        $this->object->setIlmSession($obj);
        $this->assertSame($obj, $this->object->getIlmSession());
    }

    /**
     * @covers \App\Entity\Session::addLearningMaterial
     */
    public function testAddLearningMaterial(): void
    {
        $this->entityCollectionAddTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \App\Entity\Session::removeLearningMaterial
     */
    public function testRemoveLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \App\Entity\Session::setLearningMaterials
     * @covers \App\Entity\Session::getLearningMaterials
     */
    public function testGetLearningMaterials(): void
    {
        $this->entityCollectionSetTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \App\Entity\Session::getSchool
     */
    public function testGetSchool(): void
    {
        $school = new School();
        $course = new Course();
        $session = new Session();
        $course->setSchool($school);
        $session->setCourse($course);
        $this->assertSame($school, $session->getSchool());
    }

    /**
     * @covers \App\Entity\Session::addTerm
     */
    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\Session::removeTerm
     */
    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\Session::getTerms
     * @covers \App\Entity\Session::setTerms
     */
    public function testSetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\Session::addSessionObjective
     */
    public function testAddSessionObjective(): void
    {
        $this->entityCollectionAddTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Session::removeSessionObjective
     */
    public function testRemoveSessionObjective(): void
    {
        $this->entityCollectionRemoveTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Session::setSessionObjectives
     * @covers \App\Entity\Session::getSessionObjectives
     */
    public function testGetSessionObjectives(): void
    {
        $this->entityCollectionSetTest('sessionObjective', 'SessionObjective');
    }

    /**
     * @covers \App\Entity\Session::addMeshDescriptor
     */
    public function testAddMeshDescriptor(): void
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\Session::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor(): void
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\Session::getMeshDescriptors
     * @covers \App\Entity\Session::setMeshDescriptors
     */
    public function testSetMeshDescriptors(): void
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\Session::addSequenceBlock
     */
    public function testAddSequenceBlock(): void
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\Session::removeSequenceBlock
     */
    public function testRemoveSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\Session::setSequenceBlocks
     * @covers \App\Entity\Session::getSequenceBlocks
     */
    public function testGetSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\Session::addOffering
     */
    public function testAddOffering(): void
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers \App\Entity\Session::removeOffering
     */
    public function testRemoveOffering(): void
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers \App\Entity\Session::getOfferings
     * @covers \App\Entity\Session::setOfferings
     */
    public function testSetOfferings(): void
    {
        $this->entityCollectionSetTest('offering', 'Offering');
    }

    /**
     * @covers \App\Entity\Session::addAdministrator
     */
    public function testAddAdministrator(): void
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredSession');
    }

    /**
     * @covers \App\Entity\Session::removeAdministrator
     */
    public function testRemoveAdministrator(): void
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredSession');
    }

    /**
     * @covers \App\Entity\Session::getAdministrators
     * @covers \App\Entity\Session::setAdministrators
     */
    public function testSetAdministrators(): void
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredSession');
    }

    /**
     * @covers \App\Entity\Session::addStudentAdvisor
     */
    public function testAddStudentAdvisor(): void
    {
        $this->entityCollectionAddTest('studentAdvisor', 'User', false, false, 'addStudentAdvisedSession');
    }

    /**
     * @covers \App\Entity\Session::removeStudentAdvisor
     */
    public function testRemoveStudentAdvisor(): void
    {
        $this->entityCollectionRemoveTest('studentAdvisor', 'User', false, false, false, 'removeStudentAdvisedSession');
    }

    /**
     * @covers \App\Entity\Session::getStudentAdvisors
     * @covers \App\Entity\Session::setStudentAdvisors
     */
    public function testSetStudentAdvisors(): void
    {
        $this->entityCollectionSetTest('studentAdvisor', 'User', false, false, 'addStudentAdvisedSession');
    }

    /**
     * @covers \App\Entity\Session::addExcludedSequenceBlock
     */
    public function testAddExcludedSequenceBlock(): void
    {
        $this->entityCollectionAddTest('excludedSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\Session::removeExcludedSequenceBlock
     */
    public function testRemoveExcludedSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('excludedSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\Session::setExcludedSequenceBlocks
     * @covers \App\Entity\Session::getExcludedSequenceBlocks
     */
    public function testGetExcludedSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('excludedSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\Session::setPostrequisite
     * @covers \App\Entity\Session::getPostrequisite
     */
    public function testSetPostrequisite(): void
    {
        $this->entitySetTest('postrequisite', 'Session');
    }

    /**
     * @covers \App\Entity\Session::addPrerequisite
     */
    public function testAddPrerequisite(): void
    {
        $this->entityCollectionAddTest('prerequisite', 'Session', false, false, 'setPostrequisite');
    }

    /**
     * @covers \App\Entity\Session::removePrerequisite
     */
    public function testRemovePrerequisite(): void
    {
        $this->entityCollectionRemoveTest('prerequisite', 'Session');
    }

    /**
     * @covers \App\Entity\Session::getPrerequisites
     * @covers \App\Entity\Session::setPrerequisites
     */
    public function testGetPrerequisites(): void
    {
        $this->entityCollectionSetTest('prerequisite', 'Session', false, false, 'setPostrequisite');
    }

    /**
     * @covers \App\Entity\Session::getIndexableCourses
     */
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
