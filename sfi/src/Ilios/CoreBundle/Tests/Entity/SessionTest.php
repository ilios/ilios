<?php
namespace Ilios\CoreBundle\Tests\Entity;

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

    /**
     * @covers Ilios\CoreBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getDisciplines());
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getObjectives());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setAttireRequired
     */
    public function testSetAttireRequired()
    {
        $this->booleanSetTest('attireRequired');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setEquipmentRequired
     */
    public function testSetEquipmentRequired()
    {
        $this->booleanSetTest('equipmentRequired');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setSupplemental
     */
    public function testSetSupplemental()
    {
        $this->booleanSetTest('supplemental');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setDeleted
     */
    public function testSetDeleted()
    {
        $this->booleanSetTest('deleted');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setSessionType
     */
    public function testSetSessionType()
    {
        $this->entitySetTest('sessionType', "SessionType");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', "Course");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setIlmSessionFacet
     */
    public function testSetIlmSessionFacet()
    {
        $this->entitySetTest('ilmSessionFacet', "IlmSessionFacet");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::addDiscipline
     */
    public function testAddDiscipline()
    {
        $this->entityCollectionAddTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::removeDiscipline
     */
    public function testRemoveDiscipline()
    {
        $this->entityCollectionRemoveTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
