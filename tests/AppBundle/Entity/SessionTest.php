<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Course;
use AppBundle\Entity\School;
use AppBundle\Entity\Session;
use Mockery as m;

/**
 * Tests for Entity Session
 */
class SessionTest extends EntityBase
{
    /**
     * @var Session
     */
    protected $object;

    /**
     * Instantiate a Session object
     */
    protected function setUp()
    {
        $this->object = new Session;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(

        );
        $this->object->setSessionType(m::mock('AppBundle\Entity\SessionTypeInterface'));
        $this->object->setCourse(m::mock('AppBundle\Entity\CourseInterface'));

        $this->validateNotBlanks($notBlank);
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNull = array(
            'sessionType',
            'course'
        );
        $this->validateNotNulls($notNull);

        $this->object->setSessionType(m::mock('AppBundle\Entity\SessionTypeInterface'));
        $this->object->setCourse(m::mock('AppBundle\Entity\CourseInterface'));

        $this->validate(0);
    }
    /**
     * @covers \AppBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getObjectives());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getTerms());
        $this->assertEmpty($this->object->getSequenceBlocks());
        $this->assertEmpty($this->object->getPrerequisites());
    }

    /**
     * @covers \AppBundle\Entity\Session::setTitle
     * @covers \AppBundle\Entity\Session::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Session::setAttireRequired
     * @covers \AppBundle\Entity\Session::isAttireRequired
     */
    public function testSetAttireRequired()
    {
        $this->booleanSetTest('attireRequired');
    }

    /**
     * @covers \AppBundle\Entity\Session::setEquipmentRequired
     * @covers \AppBundle\Entity\Session::isEquipmentRequired
     */
    public function testSetEquipmentRequired()
    {
        $this->booleanSetTest('equipmentRequired');
    }

    /**
     * @covers \AppBundle\Entity\Session::setSupplemental
     * @covers \AppBundle\Entity\Session::isSupplemental
     */
    public function testSetSupplemental()
    {
        $this->booleanSetTest('supplemental');
    }

    /**
     * @covers \AppBundle\Entity\Session::setAttendanceRequired
     * @covers \AppBundle\Entity\Session::isAttendanceRequired
     */
    public function testSetAttendanceRequired()
    {
        $this->booleanSetTest('attendanceRequired');
    }

    /**
     * @covers \AppBundle\Entity\Session::setPublishedAsTbd
     * @covers \AppBundle\Entity\Session::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \AppBundle\Entity\Session::setPublished
     * @covers \AppBundle\Entity\Session::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \AppBundle\Entity\Session::setInstructionalNotes
     * @covers \AppBundle\Entity\Session::getInstructionalNotes
     */
    public function testSetInstructionalNotes()
    {
        $this->basicSetTest('instructionalNotes', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Session::setSessionType
     * @covers \AppBundle\Entity\Session::getSessionType
     */
    public function testSetSessionType()
    {
        $this->entitySetTest('sessionType', "SessionType");
    }

    /**
     * @covers \AppBundle\Entity\Session::setCourse
     * @covers \AppBundle\Entity\Session::getCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', "Course");
    }

    /**
     * @covers \AppBundle\Entity\Session::setIlmSession
     * @covers \AppBundle\Entity\Session::getIlmSession
     */
    public function testSetIlmSession()
    {
        $this->assertTrue(method_exists($this->object, 'getIlmSession'), "Method getIlmSession missing");
        $this->assertTrue(method_exists($this->object, 'setIlmSession'), "Method setIlmSession missing");
        $obj = m::mock('AppBundle\Entity\IlmSession');
        $obj->shouldReceive('setSession')->with($this->object)->once();
        $this->object->setIlmSession($obj);
        $this->assertSame($obj, $this->object->getIlmSession());
    }

    /**
     * @covers \AppBundle\Entity\Session::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\Session::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\Session::setLearningMaterials
     * @covers \AppBundle\Entity\Session::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\Session::stampUpdate
     */
    public function testStampUpdate()
    {
        $now = new \DateTime();
        $this->object->stampUpdate();
        $updatedAt = $this->object->getUpdatedAt();
        $this->assertTrue($updatedAt instanceof \DateTime);
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \AppBundle\Entity\Session::getSchool
     */
    public function testGetSchool()
    {
        $school = new School();
        $course = new Course();
        $session = new Session();
        $course->setSchool($school);
        $session->setCourse($course);
        $this->assertSame($school, $session->getSchool());

        $course = new Course();
        $session = new Session();
        $session->setCourse($course);
        $this->assertNull($session->getSchool());

        $session = new Session();
        $this->assertNull($session->getSchool());
    }

    /**
     * @covers \AppBundle\Entity\Session::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \AppBundle\Entity\Session::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \AppBundle\Entity\Session::getTerms
     * @covers \AppBundle\Entity\Session::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \AppBundle\Entity\Session::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Session::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Session::getObjectives
     * @covers \AppBundle\Entity\Session::setObjectives
     */
    public function testSetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Session::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\Session::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\Session::getMeshDescriptors
     * @covers \AppBundle\Entity\Session::setMeshDescriptors
     */
    public function testSetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\Session::setSessionDescription
     * @covers \AppBundle\Entity\Session::getSessionDescription
     */
    public function testSetSessionDescription()
    {
        $this->assertTrue(method_exists($this->object, 'getSessionDescription'), "Method missing");
        $this->assertTrue(method_exists($this->object, 'setSessionDescription'), "Method missing");
        $obj = m::mock('AppBundle\Entity\SessionDescription');
        $obj->shouldReceive('setSession')->with($this->object)->once();
        $this->object->setSessionDescription($obj);
        $this->assertSame($obj, $this->object->getSessionDescription());
    }

    /**
     * @covers \AppBundle\Entity\Session::addSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\Session::removeSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\Session::setSequenceBlocks
     * @covers \AppBundle\Entity\Session::getSequenceBlocks
     */
    public function testGetSequenceBlocks()
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\Session::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers \AppBundle\Entity\Session::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers \AppBundle\Entity\Session::getOfferings
     * @covers \AppBundle\Entity\Session::setOfferings
     */
    public function testSetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering');
    }

    /**
     * @covers \AppBundle\Entity\Session::addAdministrator
     */
    public function testAddAdministrator()
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredSession');
    }

    /**
     * @covers \AppBundle\Entity\Session::removeAdministrator
     */
    public function testRemoveAdministrator()
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredSession');
    }

    /**
     * @covers \AppBundle\Entity\Session::getAdministrators
     * @covers \AppBundle\Entity\Session::setAdministrators
     */
    public function testSetAdministrators()
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredSession');
    }

    /**
     * @covers \AppBundle\Entity\Session::addExcludedSequenceBlock
     */
    public function testAddExcludedSequenceBlock()
    {
        $this->entityCollectionAddTest('excludedSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\Session::removeExcludedSequenceBlock
     */
    public function testRemoveExcludedSequenceBlock()
    {
        $this->entityCollectionRemoveTest('excludedSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\Session::setExcludedSequenceBlocks
     * @covers \AppBundle\Entity\Session::getExcludedSequenceBlocks
     */
    public function testGetExcludedSequenceBlocks()
    {
        $this->entityCollectionSetTest('excludedSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\Session::setPostrequisite
     * @covers \AppBundle\Entity\Session::getPostrequisite
     */
    public function testSetPostrequisite()
    {
        $this->entitySetTest('postrequisite', 'Session');
    }

    /**
     * @covers \AppBundle\Entity\Session::addPrerequisite
     */
    public function testAddPrerequisite()
    {
        $this->entityCollectionAddTest('prerequisite', 'Session', false, false, 'setPostrequisite');
    }

    /**
     * @covers \AppBundle\Entity\Session::removePrerequisite
     */
    public function testRemovePrerequisite()
    {
        $this->entityCollectionRemoveTest('prerequisite', 'Session');
    }

    /**
     * @covers \AppBundle\Entity\Session::getPrerequisites
     * @covers \AppBundle\Entity\Session::setPrerequisites
     */
    public function testGetPrerequisites()
    {
        $this->entityCollectionSetTest('prerequisite', 'Session', false, false, 'setPostrequisite');
    }
}
