<?php

namespace Ilios\CoreBundle\Tests\Model;

use Ilios\CoreBundle\Model\User;

/**
 * Tests for Model Objective
 */
class UserTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\User::__construct
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
     * @covers Ilios\CoreBundle\Model\User::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setLastName
     */
    public function testSetLastName()
    {
        $this->basicSetTest('lastName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getLastName
     */
    public function testGetLastName()
    {
        $this->basicGetTest('lastName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setFirstName
     */
    public function testSetFirstName()
    {
        $this->basicSetTest('firstName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getFirstName
     */
    public function testGetFirstName()
    {
        $this->basicGetTest('firstName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setMiddleName
     */
    public function testSetMiddleName()
    {
        $this->basicSetTest('middleName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getMiddleName
     */
    public function testGetMiddleName()
    {
        $this->basicGetTest('middleName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setPhone
     */
    public function testSetPhone()
    {
        $this->basicSetTest('phone', 'phone');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getPhone
     */
    public function testGetPhone()
    {
        $this->basicGetTest('phone', 'phone');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setEmail
     */
    public function testSetEmail()
    {
        $this->basicSetTest('email', 'email');
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\User::getEmail
     */
    public function testGetEmail()
    {
        $this->basicGetTest('email', 'email');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setAddedViaIlios
     */
    public function testSetAddedViaIlios()
    {
        $this->basicSetTest('addedViaIlios', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getAddedViaIlios
     */
    public function testGetAddedViaIlios()
    {
        $this->basicGetTest('addedViaIlios', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setEnabled
     */
    public function testSetEnabled()
    {
        $this->basicSetTest('enabled', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getEnabled
     */
    public function testGetEnabled()
    {
        $this->basicGetTest('enabled', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setUcUid
     */
    public function testSetUcUid()
    {
        $this->basicSetTest('ucUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getUcUid
     */
    public function testGetUcUid()
    {
        $this->basicGetTest('ucUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setOtherId
     */
    public function testSetOtherId()
    {
        $this->basicSetTest('otherId', 'string');
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\User::getOtherId
     */
    public function testGetOtherId()
    {
        $this->basicGetTest('otherId', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setExamined
     */
    public function testSetExamined()
    {
        $this->basicSetTest('examined', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getExamined
     */
    public function testGetExamined()
    {
        $this->basicGetTest('examined', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setUserSyncIgnore
     */
    public function testSetUserSyncIgnore()
    {
        $this->basicSetTest('userSyncIgnore', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getUserSyncIgnore
     */
    public function testGetUserSyncIgnore()
    {
        $this->basicGetTest('userSyncIgnore', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setPrimarySchool
     */
    public function testSetPrimarySchool()
    {
        $this->modelSetTest('primarySchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getPrimarySchool
     */
    public function testGetPrimarySchool()
    {
        $this->modelGetTest('primarySchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::setApiKey
     */
    public function testSetApiKey()
    {
        $this->modelSetTest('apiKey', 'ApiKey');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getApiKey
     */
    public function testGetApiKey()
    {
        $this->modelGetTest('apiKey', 'ApiKey');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addReminder
     */
    public function testAddReminder()
    {
        $this->modelCollectionAddTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeReminder
     */
    public function testRemoveReminder()
    {
        $this->modelCollectionRemoveTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getReminders
     */
    public function testGetReminders()
    {
        $this->modelCollectionGetTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addDirectedCourse
     */
    public function testAddDirectedCourse()
    {
        $this->modelCollectionAddTest('directedCourse', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeDirectedCourse
     */
    public function testRemoveDirectedCourse()
    {
        $this->modelCollectionRemoveTest('directedCourse', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getDirectedCourses
     */
    public function testGetDirectedCourses()
    {
        $this->modelCollectionGetTest('directedCourse', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addUserGroup
     */
    public function testAddUserGroup()
    {
        $this->modelCollectionAddTest('userGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeUserGroup
     * @todo   Implement testRemoveUserGroup().
     */
    public function testRemoveUserGroup()
    {
        $this->modelCollectionRemoveTest('userGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getUserGroups
     */
    public function testGetUserGroups()
    {
        $this->modelCollectionGetTest('userGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addInstructorUserGroup
     */
    public function testAddInstructorUserGroup()
    {
        $this->modelCollectionAddTest('instructorUserGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeInstructorUserGroup
     */
    public function testRemoveInstructorUserGroup()
    {
        $this->modelCollectionRemoveTest('instructorUserGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getInstructorUserGroups
     */
    public function testGetInstructorUserGroups()
    {
        $this->modelCollectionGetTest('instructorUserGroup', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->modelCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->modelCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->modelCollectionGetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addOffering
     */
    public function testAddOffering()
    {
        $this->modelCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->modelCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getOfferings
     */
    public function testGetOfferings()
    {
        $this->modelCollectionGetTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->modelCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->modelCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->modelCollectionGetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addAlert
     */
    public function testAddAlert()
    {
        $this->modelCollectionAddTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeAlert
     */
    public function testRemoveAlert()
    {
        
        $this->modelCollectionRemoveTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getAlerts
     */
    public function testGetAlerts()
    {
        $this->modelCollectionGetTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addRole
     */
    public function testAddRole()
    {
        $this->modelCollectionAddTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeRole
     */
    public function testRemoveRole()
    {
        $this->modelCollectionRemoveTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getRoles
     */
    public function testGetRoles()
    {
        $this->modelCollectionGetTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->modelCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->modelCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->modelCollectionGetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addPublishEvent
     */
    public function testAddPublishEvent()
    {
        $this->modelCollectionAddTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removePublishEvent
     */
    public function testRemovePublishEvent()
    {
        $this->modelCollectionRemoveTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getPublishEvents
     */
    public function testGetPublishEvents()
    {
        $this->modelCollectionGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::addReport
     */
    public function testAddReport()
    {
        $this->modelCollectionAddTest('report', 'Report');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::removeReport
     */
    public function testRemoveReport()
    {
        $this->modelCollectionRemoveTest('report', 'Report');
    }

    /**
     * @covers Ilios\CoreBundle\Model\User::getReports
     */
    public function testGetReports()
    {
        $this->modelCollectionGetTest('report', 'Report');
    }
}
