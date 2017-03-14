<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\Session;
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
        $this->object->setSessionType(m::mock('Ilios\CoreBundle\Entity\SessionTypeInterface'));
        $this->object->setCourse(m::mock('Ilios\CoreBundle\Entity\CourseInterface'));

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

        $this->object->setSessionType(m::mock('Ilios\CoreBundle\Entity\SessionTypeInterface'));
        $this->object->setCourse(m::mock('Ilios\CoreBundle\Entity\CourseInterface'));

        $this->validate(0);
    }
    /**
     * @covers \Ilios\CoreBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getObjectives());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getTerms());
        $this->assertEmpty($this->object->getSequenceBlocks());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setTitle
     * @covers \Ilios\CoreBundle\Entity\Session::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setAttireRequired
     * @covers \Ilios\CoreBundle\Entity\Session::isAttireRequired
     */
    public function testSetAttireRequired()
    {
        $this->booleanSetTest('attireRequired');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setEquipmentRequired
     * @covers \Ilios\CoreBundle\Entity\Session::isEquipmentRequired
     */
    public function testSetEquipmentRequired()
    {
        $this->booleanSetTest('equipmentRequired');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setSupplemental
     * @covers \Ilios\CoreBundle\Entity\Session::isSupplemental
     */
    public function testSetSupplemental()
    {
        $this->booleanSetTest('supplemental');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setAttendanceRequired
     * @covers \Ilios\CoreBundle\Entity\Session::isAttendanceRequired
     */
    public function testSetAttendanceRequired()
    {
        $this->booleanSetTest('attendanceRequired');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setPublishedAsTbd
     * @covers \Ilios\CoreBundle\Entity\Session::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setPublished
     * @covers \Ilios\CoreBundle\Entity\Session::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setSessionType
     * @covers \Ilios\CoreBundle\Entity\Session::getSessionType
     */
    public function testSetSessionType()
    {
        $this->entitySetTest('sessionType', "SessionType");
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setCourse
     * @covers \Ilios\CoreBundle\Entity\Session::getCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', "Course");
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setIlmSession
     * @covers \Ilios\CoreBundle\Entity\Session::getIlmSession
     */
    public function testSetIlmSession()
    {
        $this->assertTrue(method_exists($this->object, 'getIlmSession'), "Method getIlmSession missing");
        $this->assertTrue(method_exists($this->object, 'setIlmSession'), "Method setIlmSession missing");
        $obj = m::mock('Ilios\CoreBundle\Entity\IlmSession');
        $obj->shouldReceive('setSession')->with($this->object)->once();
        $this->object->setIlmSession($obj);
        $this->assertSame($obj, $this->object->getIlmSession());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setLearningMaterials
     * @covers \Ilios\CoreBundle\Entity\Session::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::stampUpdate
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
     * @covers \Ilios\CoreBundle\Entity\Session::getSchool
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
     * @covers \Ilios\CoreBundle\Entity\Session::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::getTerms
     * @covers \Ilios\CoreBundle\Entity\Session::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::getObjectives
     * @covers \Ilios\CoreBundle\Entity\Session::setObjectives
     */
    public function testSetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::getMeshDescriptors
     * @covers \Ilios\CoreBundle\Entity\Session::setMeshDescriptors
     */
    public function testSetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setSessionDescription
     * @covers \Ilios\CoreBundle\Entity\Session::getSessionDescription
     */
    public function testSetSessionDescription()
    {
        $this->assertTrue(method_exists($this->object, 'getSessionDescription'), "Method missing");
        $this->assertTrue(method_exists($this->object, 'setSessionDescription'), "Method missing");
        $obj = m::mock('Ilios\CoreBundle\Entity\SessionDescription');
        $obj->shouldReceive('setSession')->with($this->object)->once();
        $this->object->setSessionDescription($obj);
        $this->assertSame($obj, $this->object->getSessionDescription());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::addSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::removeSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::setSequenceBlocks
     * @covers \Ilios\CoreBundle\Entity\Session::getSequenceBlocks
     */
    public function testGetSequenceBlocks()
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::getOfferings
     * @covers \Ilios\CoreBundle\Entity\Session::setOfferings
     */
    public function testSetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::addAdministrator
     */
    public function testAddAdministrator()
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredSession');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::removeAdministrator
     */
    public function testRemoveAdministrator()
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredSession');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Session::getAdministrators
     * @covers \Ilios\CoreBundle\Entity\Session::setAdministrators
     */
    public function testSetAdministrators()
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredSession');
    }
}
