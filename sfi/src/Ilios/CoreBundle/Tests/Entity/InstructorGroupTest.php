<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\InstructorGroup;
use Mockery as m;

/**
 * Tests for Entity InstructorGroup
 */
class InstructorGroupTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getGroups());
        $this->assertEmpty($this->object->getIlmSessionFacets());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getUsers());
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::getInstructorGroupId
     */
    public function testGetInstructorGroupId()
    {
        $this->basicGetTest('instructorGroupId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::setSchoolId
     */
    public function testSetSchoolId()
    {
        $this->basicSetTest('schoolId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::getSchoolId
     */
    public function testGetSchoolId()
    {
        $this->basicGetTest('schoolId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::addGroup
     */
    public function testAddGroup()
    {
        $this->entityCollectionAddTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::removeGroup
     */
    public function testRemoveGroup()
    {
        $this->entityCollectionRemoveTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::getGroups
     */
    public function testGetGroups()
    {
        $this->entityCollectionGetTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::addIlmSessionFacet
     */
    public function testAddIlmSessionFacet()
    {
        $this->entityCollectionAddTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::removeIlmSessionFacet
     */
    public function testRemoveIlmSessionFacet()
    {
        $this->entityCollectionRemoveTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::getIlmSessionFacets
     */
    public function testGetIlmSessionFacets()
    {
        $this->entityCollectionGetTest('ilmSessionFacet', 'IlmSessionFacet');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::getUsers
     */
    public function testGetUsers()
    {
        $this->entityCollectionGetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructorGroup::getOfferings
     */
    public function testGetOfferings()
    {
        $this->entityCollectionGetTest('offering', 'Offering');
    }
}
