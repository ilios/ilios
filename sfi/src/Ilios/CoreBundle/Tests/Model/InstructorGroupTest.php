<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\InstructorGroup;
use Mockery as m;

/**
 * Tests for Model InstructorGroup
 */
class InstructorGroupTest extends BaseModel
{
    /**
     * @var InstructorGroup
     */
    protected $object;

    /**
     * Instantiate a InstructorGroup object
     */
    protected function setUp()
    {
        $this->object = new InstructorGroup;
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getGroups());
        $this->assertEmpty($this->object->getIlmSessionFacets());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getUsers());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::getInstructorGroupId
     */
    public function testGetInstructorGroupId()
    {
        $this->basicGetTest('instructorGroupId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::setSchoolId
     */
    public function testSetSchoolId()
    {
        $this->basicSetTest('schoolId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::getSchoolId
     */
    public function testGetSchoolId()
    {
        $this->basicGetTest('schoolId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::addGroup
     */
    public function testAddGroup()
    {
        $this->modelCollectionAddTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::removeGroup
     */
    public function testRemoveGroup()
    {
        $this->modelCollectionRemoveTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::getGroups
     */
    public function testGetGroups()
    {
        $this->modelCollectionGetTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::addIlmSessionFacet
     */
    public function testAddIlmSessionFacet()
    {
        $this->modelCollectionAddTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::removeIlmSessionFacet
     */
    public function testRemoveIlmSessionFacet()
    {
        $this->modelCollectionRemoveTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::getIlmSessionFacets
     */
    public function testGetIlmSessionFacets()
    {
        $this->modelCollectionGetTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::addUser
     */
    public function testAddUser()
    {
        $this->modelCollectionAddTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::removeUser
     */
    public function testRemoveUser()
    {
        $this->modelCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::getUsers
     */
    public function testGetUsers()
    {
        $this->modelCollectionGetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::addOffering
     */
    public function testAddOffering()
    {
        $this->modelCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->modelCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructorGroup::getOfferings
     */
    public function testGetOfferings()
    {
        $this->modelCollectionGetTest('offering', 'Offering');
    }
}
