<?php
namespace Tests\App\Entity;

use App\Entity\InstructorGroup;
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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->object->setSchool(m::mock('AppBundle\Entity\SchoolInterface'));
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNulls = array(
            'school'
        );
        $this->object->setTitle('test');

        $this->validateNotNulls($notNulls);
        $this->object->setSchool(m::mock('AppBundle\Entity\SchoolInterface'));


        $this->validate(0);
    }

    /**
     * @covers \App\Entity\InstructorGroup::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getIlmSessions());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers \App\Entity\InstructorGroup::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup', false, false, 'addInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup', false, false, false, 'removeInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::getLearnerGroups
     */
    public function testGetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup', false, false, 'addInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::addIlmSession
     */
    public function testAddIlmSession()
    {
        $this->entityCollectionAddTest('ilmSession', 'IlmSession', false, false, 'addInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::removeIlmSession
     */
    public function testRemoveIlmSession()
    {
        $this->entityCollectionRemoveTest('ilmSession', 'IlmSession', false, false, false, 'removeInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::getIlmSessions
     */
    public function testGetIlmSessions()
    {
        $this->entityCollectionSetTest('ilmSession', 'IlmSession', false, false, 'addInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers \App\Entity\InstructorGroup::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers \App\Entity\InstructorGroup::getUsers
     */
    public function testGetUsers()
    {
        $this->entityCollectionSetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\InstructorGroup::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers \App\Entity\InstructorGroup::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers \App\Entity\InstructorGroup::getOfferings
     */
    public function testGetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering');
    }
}
