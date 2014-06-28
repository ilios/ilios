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
        $this->assertEmpty($this->object->getUserGroups());
        $this->assertEmpty($this->object->getLearningMaterials());
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\User::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setLastName
     */
    public function testSetLastName()
    {
        $this->basicSetTest('lastName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getLastName
     */
    public function testGetLastName()
    {
        $this->basicGetTest('lastName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setFirstName
     */
    public function testSetFirstName()
    {
        $this->basicSetTest('firstName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getFirstName
     */
    public function testGetFirstName()
    {
        $this->basicGetTest('firstName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setMiddleName
     */
    public function testSetMiddleName()
    {
        $this->basicSetTest('middleName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getMiddleName
     */
    public function testGetMiddleName()
    {
        $this->basicGetTest('middleName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setPhone
     */
    public function testSetPhone()
    {
        $this->basicSetTest('phone', 'phone');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getPhone
     */
    public function testGetPhone()
    {
        $this->basicGetTest('phone', 'phone');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setEmail
     */
    public function testSetEmail()
    {
        $this->basicSetTest('email', 'email');
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\User::getEmail
     */
    public function testGetEmail()
    {
        $this->basicGetTest('email', 'email');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setAddedViaIlios
     */
    public function testSetAddedViaIlios()
    {
        $this->basicSetTest('addedViaIlios', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getAddedViaIlios
     */
    public function testGetAddedViaIlios()
    {
        $this->basicGetTest('addedViaIlios', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setEnabled
     */
    public function testSetEnabled()
    {
        $this->basicSetTest('enabled', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getEnabled
     */
    public function testGetEnabled()
    {
        $this->basicGetTest('enabled', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setUcUid
     */
    public function testSetUcUid()
    {
        $this->basicSetTest('ucUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getUcUid
     */
    public function testGetUcUid()
    {
        $this->basicGetTest('ucUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setOtherId
     */
    public function testSetOtherId()
    {
        $this->basicSetTest('otherId', 'string');
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\User::getOtherId
     */
    public function testGetOtherId()
    {
        $this->basicGetTest('otherId', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setExamined
     */
    public function testSetExamined()
    {
        $this->basicSetTest('examined', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getExamined
     */
    public function testGetExamined()
    {
        $this->basicGetTest('examined', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setUserSyncIgnore
     */
    public function testSetUserSyncIgnore()
    {
        $this->basicSetTest('userSyncIgnore', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getUserSyncIgnore
     */
    public function testGetUserSyncIgnore()
    {
        $this->basicGetTest('userSyncIgnore', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setPrimarySchool
     */
    public function testSetPrimarySchool()
    {
        $this->entitySetTest('primarySchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getPrimarySchool
     */
    public function testGetPrimarySchool()
    {
        $this->entityGetTest('primarySchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setApiKey
     */
    public function testSetApiKey()
    {
        $this->entitySetTest('apiKey', 'ApiKey');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getApiKey
     */
    public function testGetApiKey()
    {
        $this->entityGetTest('apiKey', 'ApiKey');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addReminder
     */
    public function testAddReminder()
    {
        $this->entityCollectionAddTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeReminder
     */
    public function testRemoveReminder()
    {
        $this->entityCollectionRemoveTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getReminders
     */
    public function testGetReminders()
    {
        $this->entityCollectionGetTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addDirectedCourse
     */
    public function testAddDirectedCourse()
    {
        $this->entityCollectionAddTest('directedCourse', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeDirectedCourse
     */
    public function testRemoveDirectedCourse()
    {
        $this->entityCollectionRemoveTest('directedCourse', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getDirectedCourses
     */
    public function testGetDirectedCourses()
    {
        $this->entityCollectionGetTest('directedCourse', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addUserGroup
     */
    public function testAddUserGroup()
    {
        $this->entityCollectionAddTest('userGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeUserGroup
     * @todo   Implement testRemoveUserGroup().
     */
    public function testRemoveUserGroup()
    {
        $this->entityCollectionRemoveTest('userGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getUserGroups
     */
    public function testGetUserGroups()
    {
        $this->entityCollectionGetTest('userGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addInstructorUserGroup
     */
    public function testAddInstructorUserGroup()
    {
        $this->entityCollectionAddTest('instructorUserGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeInstructorUserGroup
     */
    public function testRemoveInstructorUserGroup()
    {
        $this->entityCollectionRemoveTest('instructorUserGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getInstructorUserGroups
     */
    public function testGetInstructorUserGroups()
    {
        $this->entityCollectionGetTest('instructorUserGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->entityCollectionGetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getOfferings
     */
    public function testGetOfferings()
    {
        $this->entityCollectionGetTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionGetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeAlert
     */
    public function testRemoveAlert()
    {
        
        $this->entityCollectionRemoveTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionGetTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addRole
     */
    public function testAddRole()
    {
        $this->entityCollectionAddTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeRole
     */
    public function testRemoveRole()
    {
        $this->entityCollectionRemoveTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getRoles
     */
    public function testGetRoles()
    {
        $this->entityCollectionGetTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionGetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addPublishEvent
     */
    public function testAddPublishEvent()
    {
        $this->entityCollectionAddTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removePublishEvent
     */
    public function testRemovePublishEvent()
    {
        $this->entityCollectionRemoveTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getPublishEvents
     */
    public function testGetPublishEvents()
    {
        $this->entityCollectionGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addReport
     */
    public function testAddReport()
    {
        $this->entityCollectionAddTest('report', 'Report');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::removeReport
     */
    public function testRemoveReport()
    {
        $this->entityCollectionRemoveTest('report', 'Report');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getReports
     */
    public function testGetReports()
    {
        $this->entityCollectionGetTest('report', 'Report');
    }
}
