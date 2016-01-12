<?php
namespace Ilios\CoreBundle\Tests\Entity;

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
        $this->validateNotBlanks($notBlank);
        $this->validate(0);
    }
    /**
     * @covers Ilios\CoreBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getTopics());
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getObjectives());
        $this->assertEmpty($this->object->getOfferings());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setTitle
     * @covers Ilios\CoreBundle\Entity\Session::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setAttireRequired
     * @covers Ilios\CoreBundle\Entity\Session::isAttireRequired
     */
    public function testSetAttireRequired()
    {
        $this->booleanSetTest('attireRequired');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setEquipmentRequired
     * @covers Ilios\CoreBundle\Entity\Session::isEquipmentRequired
     */
    public function testSetEquipmentRequired()
    {
        $this->booleanSetTest('equipmentRequired');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setSupplemental
     * @covers Ilios\CoreBundle\Entity\Session::isSupplemental
     */
    public function testSetSupplemental()
    {
        $this->booleanSetTest('supplemental');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setPublishedAsTbd
     * @covers Ilios\CoreBundle\Entity\Session::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setPublished
     * @covers Ilios\CoreBundle\Entity\Session::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setSessionType
     * @covers Ilios\CoreBundle\Entity\Session::getSessionType
     */
    public function testSetSessionType()
    {
        $this->entitySetTest('sessionType', "SessionType");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setCourse
     * @covers Ilios\CoreBundle\Entity\Session::getCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', "Course");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setIlmSession
     * @covers Ilios\CoreBundle\Entity\Session::getIlmSession
     */
    public function testSetIlmSession()
    {
        $this->entitySetTest('ilmSession', "IlmSession");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::addTopic
     */
    public function testAddTopic()
    {
        $this->entityCollectionAddTest('topic', 'Topic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setLearningMaterials
     * @covers Ilios\CoreBundle\Entity\Session::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::stampUpdate
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
     * @covers Ilios\CoreBundle\Entity\Session::getSchool
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
}
