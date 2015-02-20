<?php

namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\User;

/**
 * Tests for Entity Objective
 */
class UserTest extends EntityBase
{
    /**
     * @var User
     */
    protected $object;

    /**
     * Instantiate a Objective object
     */
    protected function setUp()
    {
        $this->object = new User;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
        $this->assertEmpty($this->object->getDirectedCourses());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getInstructorUserGroups());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getReminders());
        $this->assertEmpty($this->object->getRoles());
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getLearningMaterials());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setLastName
     * @covers Ilios\CoreBundle\Entity\User::getLastName
     */
    public function testSetLastName()
    {
        $this->basicSetTest('lastName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setFirstName
     * @covers Ilios\CoreBundle\Entity\User::getFirstName
     */
    public function testSetFirstName()
    {
        $this->basicSetTest('firstName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setMiddleName
     * @covers Ilios\CoreBundle\Entity\User::getMiddleName
     */
    public function testSetMiddleName()
    {
        $this->basicSetTest('middleName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setPhone
     * @covers Ilios\CoreBundle\Entity\User::getPhone
     */
    public function testSetPhone()
    {
        $this->basicSetTest('phone', 'phone');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setEmail
     * @covers Ilios\CoreBundle\Entity\User::getEmail
     */
    public function testSetEmail()
    {
        $this->basicSetTest('email', 'email');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setAddedViaIlios
     * @covers Ilios\CoreBundle\Entity\User::isAddedViaIlios
     */
    public function testSetAddedViaIlios()
    {
        $this->booleanSetTest('addedViaIlios');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setEnabled
     * @covers Ilios\CoreBundle\Entity\User::isEnabled
     */
    public function testSetEnabled()
    {
        $this->booleanSetTest('enabled');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setUcUid
     * @covers Ilios\CoreBundle\Entity\User::getUcUid
     */
    public function testSetUcUid()
    {
        $this->basicSetTest('ucUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setOtherId
     * @covers Ilios\CoreBundle\Entity\User::getOtherId
     */
    public function testSetOtherId()
    {
        $this->basicSetTest('otherId', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setExamined
     * @covers Ilios\CoreBundle\Entity\User::isExamined
     */
    public function testSetExamined()
    {
        $this->booleanSetTest('examined');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setUserSyncIgnore
     * @covers Ilios\CoreBundle\Entity\User::isUserSyncIgnore
     */
    public function testSetUserSyncIgnore()
    {
        $this->booleanSetTest('userSyncIgnore');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setPrimarySchool
     * @covers Ilios\CoreBundle\Entity\User::getPrimarySchool
     */
    public function testSetPrimarySchool()
    {
        $this->entitySetTest('primarySchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setApiKey
     * @covers Ilios\CoreBundle\Entity\User::getApiKey
     */
    public function testSetApiKey()
    {
        $this->entitySetTest('apiKey', 'ApiKey');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addReminder
     */
    public function testAddReminder()
    {
        $this->entityCollectionAddTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setReminders
     * @covers Ilios\CoreBundle\Entity\User::getReminders
     * @covers Ilios\CoreBundle\Entity\User::getReminders
     */
    public function testSetReminders()
    {
        $this->entityCollectionSetTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addDirectedCourse
     */
    public function testAddDirectedCourse()
    {
        $this->entityCollectionAddTest('directedCourse', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getDirectedCourses
     */
    public function testSetDirectedCourses()
    {
        $this->entityCollectionSetTest('directedCourse', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getLearnerGroups
     */
    public function testSetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addInstructorUserGroup
     */
    public function testAddInstructorUserGroup()
    {
        $this->entityCollectionAddTest('instructorUserGroup', 'LearnerGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getInstructorUserGroups
     */
    public function testSetInstructorUserGroups()
    {
        $this->entityCollectionSetTest('instructorUserGroup', 'LearnerGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getInstructorGroups
     */
    public function testSetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getOfferings
     */
    public function testSetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getProgramYears
     */
    public function testSetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getAlerts
     */
    public function testSetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addRole
     */
    public function testAddRole()
    {
        $this->entityCollectionAddTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getRoles
     */
    public function testSetRoles()
    {
        $this->entityCollectionSetTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getLearningMaterials
     */
    public function testSetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addPublishEvent
     */
    public function testAddPublishEvent()
    {
        $this->entityCollectionAddTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getPublishEvents
     */
    public function testSetPublishEvents()
    {
        $this->entityCollectionSetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addReport
     */
    public function testAddReport()
    {
        $this->entityCollectionAddTest('report', 'Report');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getReports
     */
    public function testSetReports()
    {
        $this->entityCollectionSetTest('report', 'Report');
    }
}
