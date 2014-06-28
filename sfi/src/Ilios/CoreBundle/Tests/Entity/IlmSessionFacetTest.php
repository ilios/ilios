<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\IlmSessionFacet;
use Mockery as m;

/**
 * Tests for Entity IlmSessionFacet
 */
class IlmSessionFacetTest extends EntityBase
{
    /**
     * @var IlmSessionFacet
     */
    protected $object;

    /**
     * Instantiate a IlmSessionFacet object
     */
    protected function setUp()
    {
        $this->object = new IlmSessionFacet;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getGroups());
        $this->assertEmpty($this->object->getInstructors());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getLearners());
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getIlmSessionFacetId
     */
    public function testGetIlmSessionFacetId()
    {
        $this->basicGetTest('ilmSessionFacetId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::setHours
     */
    public function testSetHours()
    {
        $this->basicSetTest('hours', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getHours
     */
    public function testGetHours()
    {
        $this->basicGetTest('hours', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::setDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getDueDate
     */
    public function testGetDueDate()
    {
        $this->basicGetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::addGroup
     */
    public function testAddGroup()
    {
        $this->entityCollectionAddTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::removeGroup
     */
    public function testRemoveGroup()
    {
        $this->entityCollectionRemoveTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getGroups
     */
    public function testGetGroups()
    {
        $this->entityCollectionGetTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->entityCollectionGetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::addInstructor
     */
    public function testAddInstructor()
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::removeInstructor
     */
    public function testRemoveInstructor()
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getInstructors
     */
    public function testGetInstructors()
    {
        $this->entityCollectionGetTest('instructor', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::addLearner
     */
    public function testAddLearner()
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::removeLearner
     */
    public function testRemoveLearner()
    {
        $this->entityCollectionRemoveTest('learner', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getLearners
     */
    public function testGetLearners()
    {
        $this->entityCollectionGetTest('learner', 'User');
    }
}
