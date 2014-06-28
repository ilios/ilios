<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\Group;
use Mockery as m;

/**
 * Tests for Entity Group
 */
class GroupTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\Group::__construct
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
     * @covers Ilios\CoreBundle\Entity\Group::getGroupId
     */
    public function testGetGroupId()
    {
        $this->basicGetTest('groupId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::setInstructors
     */
    public function testSetInstructors()
    {
        $this->basicSetTest('instructors', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getInstructors
     */
    public function testGetInstructors()
    {
        $this->basicGetTest('instructors', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::setLocation
     */
    public function testSetLocation()
    {
        $this->basicSetTest('location', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getLocation
     */
    public function testGetLocation()
    {
        $this->basicGetTest('location', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::setCohort
     */
    public function testSetCohort()
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getCohort
     */
    public function testGetCohort()
    {
        $this->entityGetTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getUsers
     */
    public function testGetUsers()
    {
        $this->entityCollectionGetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::addInstructorUser
     */
    public function testAddInstructorUser()
    {
        $this->entityCollectionAddTest('instructorUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::removeInstructorUser
     */
    public function testRemoveInstructorUser()
    {
        $this->entityCollectionRemoveTest('instructorUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getInstructorUsers
     */
    public function testGetInstructorUsers()
    {
        $this->entityCollectionGetTest('instructorUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->entityCollectionGetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::addIlmSessionFacet
     */
    public function testAddIlmSessionFacet()
    {
        $this->entityCollectionAddTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::removeIlmSessionFacet
     */
    public function testRemoveIlmSessionFacet()
    {
        $this->entityCollectionRemoveTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getIlmSessionFacets
     */
    public function testGetIlmSessionFacets()
    {
        $this->entityCollectionGetTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getOfferings
     */
    public function testGetOfferings()
    {
        $this->entityCollectionGetTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::addParent
     */
    public function testAddParent()
    {
        $this->entityCollectionAddTest('parent', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::removeParent
     */
    public function testRemoveParent()
    {
        $this->entityCollectionRemoveTest('parent', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Group::getParents
     */
    public function testGetParents()
    {
        $this->entityCollectionGetTest('parent', 'Group');
    }
}
