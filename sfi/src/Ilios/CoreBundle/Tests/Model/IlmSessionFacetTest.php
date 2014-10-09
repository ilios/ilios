<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\IlmSessionFacet;
use Mockery as m;

/**
 * Tests for Model IlmSessionFacet
 */
class IlmSessionFacetTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getGroups());
        $this->assertEmpty($this->object->getInstructors());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getLearners());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::getIlmSessionFacetId
     */
    public function testGetIlmSessionFacetId()
    {
        $this->basicGetTest('ilmSessionFacetId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::setHours
     */
    public function testSetHours()
    {
        $this->basicSetTest('hours', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::getHours
     */
    public function testGetHours()
    {
        $this->basicGetTest('hours', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::setDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::getDueDate
     */
    public function testGetDueDate()
    {
        $this->basicGetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::addGroup
     */
    public function testAddGroup()
    {
        $this->modelCollectionAddTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::removeGroup
     */
    public function testRemoveGroup()
    {
        $this->modelCollectionRemoveTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::getGroups
     */
    public function testGetGroups()
    {
        $this->modelCollectionGetTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->modelCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->modelCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->modelCollectionGetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::addInstructor
     */
    public function testAddInstructor()
    {
        $this->modelCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::removeInstructor
     */
    public function testRemoveInstructor()
    {
        $this->modelCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::getInstructors
     */
    public function testGetInstructors()
    {
        $this->modelCollectionGetTest('instructor', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::addLearner
     */
    public function testAddLearner()
    {
        $this->modelCollectionAddTest('learner', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::removeLearner
     */
    public function testRemoveLearner()
    {
        $this->modelCollectionRemoveTest('learner', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IlmSessionFacet::getLearners
     */
    public function testGetLearners()
    {
        $this->modelCollectionGetTest('learner', 'User');
    }
}
