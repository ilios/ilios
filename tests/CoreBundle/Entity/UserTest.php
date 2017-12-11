<?php

namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\User;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'lastName',
            'firstName',
            'email'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setLastName('Andrews');
        $this->object->setFirstName('Julia');
        $this->object->setEmail('sanders@ucsf.edu');
        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
        $this->assertEmpty($this->object->getDirectedCourses());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getInstructedLearnerGroups());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getReminders());
        $this->assertEmpty($this->object->getRoles());
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getLearningMaterials());
        $this->assertEmpty($this->object->getCohorts());
        $this->assertEmpty($this->object->getAuditLogs());
        $this->assertEmpty($this->object->getReports());
        $this->assertEmpty($this->object->getPendingUserUpdates());
        $this->assertEmpty($this->object->getPermissions());
        $this->assertEmpty($this->object->getAdministeredSessions());
        $this->assertEmpty($this->object->getAdministeredCourses());
        $this->assertEmpty($this->object->getDirectedSchools());
        $this->assertEmpty($this->object->getAdministeredSchools());
        $this->assertEmpty($this->object->getDirectedPrograms());
        $this->assertFalse($this->object->isRoot());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setLastName
     * @covers \Ilios\CoreBundle\Entity\User::getLastName
     */
    public function testSetLastName()
    {
        $this->basicSetTest('lastName', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setFirstName
     * @covers \Ilios\CoreBundle\Entity\User::getFirstName
     */
    public function testSetFirstName()
    {
        $this->basicSetTest('firstName', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setMiddleName
     * @covers \Ilios\CoreBundle\Entity\User::getMiddleName
     */
    public function testSetMiddleName()
    {
        $this->basicSetTest('middleName', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setPhone
     * @covers \Ilios\CoreBundle\Entity\User::getPhone
     */
    public function testSetPhone()
    {
        $this->basicSetTest('phone', 'phone');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setEmail
     * @covers \Ilios\CoreBundle\Entity\User::getEmail
     */
    public function testSetEmail()
    {
        $this->basicSetTest('email', 'email');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setAddedViaIlios
     * @covers \Ilios\CoreBundle\Entity\User::isAddedViaIlios
     */
    public function testSetAddedViaIlios()
    {
        $this->booleanSetTest('addedViaIlios');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setEnabled
     * @covers \Ilios\CoreBundle\Entity\User::isEnabled
     */
    public function testSetEnabled()
    {
        $this->booleanSetTest('enabled');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setCampusId
     * @covers \Ilios\CoreBundle\Entity\User::getCampusId
     */
    public function testSetCampusId()
    {
        $this->basicSetTest('campusId', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setOtherId
     * @covers \Ilios\CoreBundle\Entity\User::getOtherId
     */
    public function testSetOtherId()
    {
        $this->basicSetTest('otherId', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setExamined
     * @covers \Ilios\CoreBundle\Entity\User::isExamined
     */
    public function testSetExamined()
    {
        $this->booleanSetTest('examined');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setUserSyncIgnore
     * @covers \Ilios\CoreBundle\Entity\User::isUserSyncIgnore
     */
    public function testSetUserSyncIgnore()
    {
        $this->booleanSetTest('userSyncIgnore');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setIcsFeedKey
     * @covers \Ilios\CoreBundle\Entity\User::generateIcsFeedKey
     */
    public function testSetIcsFeedKey()
    {
        $this->basicSetTest('icsFeedKey', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setSchool
     * @covers \Ilios\CoreBundle\Entity\User::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addReminder
     */
    public function testAddReminder()
    {
        $this->entityCollectionAddTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeReminder
     */
    public function testRemoveReminder()
    {
        $this->entityCollectionRemoveTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setReminders
     * @covers \Ilios\CoreBundle\Entity\User::getReminders
     */
    public function testSetReminders()
    {
        $this->entityCollectionSetTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addAuditLog
     */
    public function testAddAuditLog()
    {
        $this->entityCollectionAddTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeAuditLog
     */
    public function testRemoveAuditLog()
    {
        $this->entityCollectionRemoveTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setAuditLogs
     * @covers \Ilios\CoreBundle\Entity\User::getAuditLogs
     */
    public function testSetAuditLogs()
    {
        $this->entityCollectionSetTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addPermission
     */
    public function testAddPermission()
    {
        $this->entityCollectionAddTest('permission', 'Permission');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removePermission
     */
    public function testRemovePermission()
    {
        $this->entityCollectionRemoveTest('permission', 'Permission');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setPermissions
     * @covers \Ilios\CoreBundle\Entity\User::getPermissions
     */
    public function testSetPermissions()
    {
        $this->entityCollectionSetTest('permission', 'Permission');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addDirectedCourse
     */
    public function testAddDirectedCourse()
    {
        $this->entityCollectionAddTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeDirectedCourse
     */
    public function testRemoveDirectedCourse()
    {
        $this->entityCollectionRemoveTest('directedCourse', 'Course', false, false, false, 'removeDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getDirectedCourses
     * @covers \Ilios\CoreBundle\Entity\User::setDirectedCourses
     */
    public function testGetDirectedCourses()
    {
        $this->entityCollectionSetTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addAdministeredSession
     */
    public function testAddAdministeredSession()
    {
        $this->entityCollectionAddTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeAdministeredSession
     */
    public function testRemoveAdministeredSession()
    {
        $this->entityCollectionRemoveTest('administeredSession', 'Session', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getAdministeredSessions
     * @covers \Ilios\CoreBundle\Entity\User::setAdministeredSessions
     */
    public function testGetAdministeredSessions()
    {
        $this->entityCollectionSetTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup', false, false, false, 'removeUser');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getLearnerGroups
     */
    public function testSetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addInstructedLearnerGroup
     */
    public function testAddInstructedLearnerGroup()
    {
        $this->entityCollectionAddTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeInstructedLearnerGroup
     */
    public function testRemoveInstructedLearnerGroup()
    {
        $this->entityCollectionRemoveTest(
            'instructedLearnerGroup',
            'LearnerGroup',
            false,
            false,
            false,
            'removeInstructor'
        );
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getInstructedLearnerGroups
     * @covers \Ilios\CoreBundle\Entity\User::setInstructedLearnerGroups
     */
    public function testGetInstructedLearnerGroups()
    {
        $this->entityCollectionSetTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup', false, false, false, 'removeUser');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getInstructorGroups
     */
    public function testSetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearner');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getOfferings
     */
    public function testSetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getProgramYears
     */
    public function testSetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeInstigator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getAlerts
     */
    public function testSetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addRole
     */
    public function testAddRole()
    {
        $this->entityCollectionAddTest('role', 'UserRole');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeRole
     */
    public function testRemoveRole()
    {
        $this->entityCollectionRemoveTest('role', 'UserRole');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getRoles
     * @covers \Ilios\CoreBundle\Entity\User::setRoles
     */
    public function testSetRoles()
    {
        $this->entityCollectionSetTest('role', 'UserRole');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getLearningMaterials
     */
    public function testSetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addReport
     */
    public function testAddReport()
    {
        $this->entityCollectionAddTest('report', 'Report');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeReport
     */
    public function testRemoveReport()
    {
        $this->entityCollectionRemoveTest('report', 'Report');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getReports
     * @covers \Ilios\CoreBundle\Entity\User::setReports
     */
    public function testSetReports()
    {
        $this->entityCollectionSetTest('report', 'Report');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addCohort
     */
    public function testAddCohort()
    {
        $this->entityCollectionAddTest('cohort', 'Cohort');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeCohort
     */
    public function testRemoveCohort()
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getCohorts
     * @covers \Ilios\CoreBundle\Entity\User::setCohorts
     */
    public function testSetCohorts()
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock('Ilios\CoreBundle\Entity\Cohort');
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $obj2 = m::mock('Ilios\CoreBundle\Entity\Cohort');
        $this->object->setCohorts(new ArrayCollection([$obj2]));
        $this->assertNull($this->object->getPrimaryCohort());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addInstructedOffering
     */
    public function testAddInstructedOffering()
    {
        $this->entityCollectionAddTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeInstructedOffering
     */
    public function testRemoveInstructedOffering()
    {
        $this->entityCollectionRemoveTest('instructedOffering', 'Offering', false, false, false, 'removeInstructor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getInstructedOfferings
     * @covers \Ilios\CoreBundle\Entity\User::setInstructedOfferings
     */
    public function testSetInstructedOffering()
    {
        $this->entityCollectionSetTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addInstructorIlmSession
     */
    public function testAddInstructorIlmSessions()
    {
        $this->entityCollectionAddTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeInstructorIlmSession
     */
    public function testRemoveInstructorIlmSessions()
    {
        $this->entityCollectionRemoveTest(
            'instructorIlmSession',
            'IlmSession',
            false,
            false,
            false,
            'removeInstructor'
        );
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getInstructorIlmSessions
     * @covers \Ilios\CoreBundle\Entity\User::setInstructorIlmSessions
     */
    public function testGetInstructorIlmSessions()
    {
        $this->entityCollectionSetTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addLearnerIlmSession
     */
    public function testAddLearnerIlmSessions()
    {
        $this->entityCollectionAddTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeLearnerIlmSession
     */
    public function testRemoveLearnerIlmSessions()
    {
        $this->entityCollectionRemoveTest(
            'learnerIlmSession',
            'IlmSession',
            false,
            false,
            false,
            'removeLearner'
        );
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getLearnerIlmSessions
     * @covers \Ilios\CoreBundle\Entity\User::setLearnerIlmSessions
     */
    public function testGetLearnerIlmSessions()
    {
        $this->entityCollectionSetTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setPrimaryCohort
     * @covers \Ilios\CoreBundle\Entity\User::getPrimaryCohort
     */
    public function testSetPrimaryCohort()
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock('Ilios\CoreBundle\Entity\Cohort');
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $this->assertSame($obj, $this->object->getPrimaryCohort());
        $this->assertTrue($this->object->getCohorts()->contains($obj));
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addPendingUserUpdate
     */
    public function testAddPendingUserUpdates()
    {
        $this->entityCollectionAddTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removePendingUserUpdate
     */
    public function testRemovePendingUserUpdates()
    {
        $this->entityCollectionRemoveTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getPendingUserUpdates
     * @covers \Ilios\CoreBundle\Entity\User::setPendingUserUpdates
     */
    public function testGetPendingUserUpdates()
    {
        $this->entityCollectionSetTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addDirectedSchool
     */
    public function testAddDirectedSchool()
    {
        $this->entityCollectionAddTest('directedSchool', 'School', false, false, 'addDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeDirectedSchool
     */
    public function testRemoveDirectedSchool()
    {
        $this->entityCollectionRemoveTest('directedSchool', 'School', false, false, false, 'removeDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getDirectedSchools
     * @covers \Ilios\CoreBundle\Entity\User::setDirectedSchools
     */
    public function testGetDirectedSchools()
    {
        $this->entityCollectionSetTest('directedSchool', 'School', false, false, 'addDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addAdministeredCourse
     */
    public function testAddAdministeredCourse()
    {
        $this->entityCollectionAddTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeAdministeredCourse
     */
    public function testRemoveAdministeredCourse()
    {
        $this->entityCollectionRemoveTest('administeredCourse', 'Course', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getAdministeredCourses
     * @covers \Ilios\CoreBundle\Entity\User::setAdministeredCourses
     */
    public function testGetAdministeredCourses()
    {
        $this->entityCollectionSetTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addAdministeredSchool
     */
    public function testAddAdministeredSchool()
    {
        $this->entityCollectionAddTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeAdministeredSchool
     */
    public function testRemoveAdministeredSchool()
    {
        $this->entityCollectionRemoveTest('administeredSchool', 'School', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getAdministeredSchools
     * @covers \Ilios\CoreBundle\Entity\User::setAdministeredSchools
     */
    public function testGetAdministeredSchools()
    {
        $this->entityCollectionSetTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addDirectedProgram
     */
    public function testAddDirectedProgram()
    {
        $this->entityCollectionAddTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeDirectedProgram
     */
    public function testRemoveDirectedProgram()
    {
        $this->entityCollectionRemoveTest('directedProgram', 'Program', false, false, false, 'removeDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getDirectedPrograms
     * @covers \Ilios\CoreBundle\Entity\User::setDirectedPrograms
     */
    public function testGetDirectedPrograms()
    {
        $this->entityCollectionSetTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::isRoot
     * @covers \Ilios\CoreBundle\Entity\User::setRoot
     */
    public function testIsRoot()
    {
        $this->booleanSetTest('root');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setAuthentication()
     * @covers \Ilios\CoreBundle\Entity\User::getAuthentication()
     */
    public function testSetAuthentication()
    {
        $this->assertTrue(method_exists($this->object, 'getAuthentication'), "Method getAuthentication missing");
        $this->assertTrue(method_exists($this->object, 'setAuthentication'), "Method setAuthentication missing");
        $obj = m::mock('Ilios\CoreBundle\Entity\Authentication');
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::setAuthentication()
     */
    public function testSetAuthenticationNull()
    {
        $obj = m::mock('Ilios\CoreBundle\Entity\Authentication');
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
        $this->object->setAuthentication(null);
        $this->assertSame(null, $this->object->getAuthentication());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::addAdministeredCurriculumInventoryReport
     */
    public function testAddAdministeredCurriculumInventoryReport()
    {
        $this->entityCollectionAddTest(
            'administeredCurriculumInventoryReport',
            'CurriculumInventoryReport',
            false,
            false,
            'addAdministrator'
        );
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::removeAdministeredCurriculumInventoryReport
     */
    public function testRemoveAdministeredCurriculumInventoryReport()
    {
        $this->entityCollectionRemoveTest(
            'administeredCurriculumInventoryReport',
            'CurriculumInventoryReport',
            false,
            false,
            false,
            'removeAdministrator'
        );
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\User::getAdministeredCurriculumInventoryReports
     * @covers \Ilios\CoreBundle\Entity\User::setAdministeredCurriculumInventoryReports
     */
    public function testGetAdministeredCurriculumInventoryReports()
    {
        $this->entityCollectionSetTest(
            'administeredCurriculumInventoryReport',
            'CurriculumInventoryReport',
            false,
            false,
            'addAdministrator'
        );
    }
}
