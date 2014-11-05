<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Group;
use Mockery as m;

/**
 * Tests for Model Group
 */
class GroupTest extends BaseModel
{
    /**
     * @var Group
     */
    protected $object;

    /**
     * Instantiate a Group object
     */
    protected function setUp()
    {
        $this->object = new Group;
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getIlmSessionFacets());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getInstructorUsers());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getUsers());
        $this->assertEmpty($this->object->getParents());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\Group::getGroupId
     */
    public function testGetGroupId()
    {
        $this->basicGetTest('groupId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::setInstructors
     */
    public function testSetInstructors()
    {
        $this->basicSetTest('instructors', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getInstructors
     */
    public function testGetInstructors()
    {
        $this->basicGetTest('instructors', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::setLocation
     */
    public function testSetLocation()
    {
        $this->basicSetTest('location', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getLocation
     */
    public function testGetLocation()
    {
        $this->basicGetTest('location', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::setCohort
     */
    public function testSetCohort()
    {
        $this->modelSetTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getCohort
     */
    public function testGetCohort()
    {
        $this->modelGetTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::addUser
     */
    public function testAddUser()
    {
        $this->modelCollectionAddTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::removeUser
     */
    public function testRemoveUser()
    {
        $this->modelCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getUsers
     */
    public function testGetUsers()
    {
        $this->modelCollectionGetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::addInstructorUser
     */
    public function testAddInstructorUser()
    {
        $this->modelCollectionAddTest('instructorUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::removeInstructorUser
     */
    public function testRemoveInstructorUser()
    {
        $this->modelCollectionRemoveTest('instructorUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getInstructorUsers
     */
    public function testGetInstructorUsers()
    {
        $this->modelCollectionGetTest('instructorUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->modelCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->modelCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->modelCollectionGetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::addIlmSessionFacet
     */
    public function testAddIlmSessionFacet()
    {
        $this->modelCollectionAddTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::removeIlmSessionFacet
     */
    public function testRemoveIlmSessionFacet()
    {
        $this->modelCollectionRemoveTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getIlmSessionFacets
     */
    public function testGetIlmSessionFacets()
    {
        $this->modelCollectionGetTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::addOffering
     */
    public function testAddOffering()
    {
        $this->modelCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->modelCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getOfferings
     */
    public function testGetOfferings()
    {
        $this->modelCollectionGetTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::addParent
     */
    public function testAddParent()
    {
        $this->modelCollectionAddTest('parent', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::removeParent
     */
    public function testRemoveParent()
    {
        $this->modelCollectionRemoveTest('parent', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Group::getParents
     */
    public function testGetParents()
    {
        $this->modelCollectionGetTest('parent', 'Group');
    }
}
