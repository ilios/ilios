<?php

namespace App\Tests\Entity;

use App\Entity\User;
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
     * @covers \App\Entity\User::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
        $this->assertEmpty($this->object->getDirectedCourses());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getInstructedLearnerGroups());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getRoles());
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getLearningMaterials());
        $this->assertEmpty($this->object->getCohorts());
        $this->assertEmpty($this->object->getAuditLogs());
        $this->assertEmpty($this->object->getReports());
        $this->assertEmpty($this->object->getPendingUserUpdates());
        $this->assertEmpty($this->object->getAdministeredSessions());
        $this->assertEmpty($this->object->getAdministeredCourses());
        $this->assertEmpty($this->object->getDirectedSchools());
        $this->assertEmpty($this->object->getAdministeredSchools());
        $this->assertEmpty($this->object->getDirectedPrograms());
        $this->assertFalse($this->object->isRoot());
    }

    /**
     * @covers \App\Entity\User::setLastName
     * @covers \App\Entity\User::getLastName
     */
    public function testSetLastName()
    {
        $this->basicSetTest('lastName', 'string');
    }

    /**
     * @covers \App\Entity\User::setFirstName
     * @covers \App\Entity\User::getFirstName
     */
    public function testSetFirstName()
    {
        $this->basicSetTest('firstName', 'string');
    }

    /**
     * @covers \App\Entity\User::setMiddleName
     * @covers \App\Entity\User::getMiddleName
     */
    public function testSetMiddleName()
    {
        $this->basicSetTest('middleName', 'string');
    }

    /**
     * @covers \App\Entity\User::setDisplayName
     * @covers \App\Entity\User::getDisplayName
     */
    public function testSetDisplayName()
    {
        $this->basicSetTest('displayName', 'string');
    }

    /**
     * @covers \App\Entity\User::setPhone
     * @covers \App\Entity\User::getPhone
     */
    public function testSetPhone()
    {
        $this->basicSetTest('phone', 'phone');
    }

    /**
     * @covers \App\Entity\User::setEmail
     * @covers \App\Entity\User::getEmail
     */
    public function testSetEmail()
    {
        $this->basicSetTest('email', 'email');
    }

    /**
     * @covers \App\Entity\User::setPreferredEmail
     * @covers \App\Entity\User::getPreferredEmail
     */
    public function testSetPreferredEmail()
    {
        $this->basicSetTest('preferredEmail', 'email');
    }

    /**
     * @covers \App\Entity\User::setAddedViaIlios
     * @covers \App\Entity\User::isAddedViaIlios
     */
    public function testSetAddedViaIlios()
    {
        $this->booleanSetTest('addedViaIlios');
    }

    /**
     * @covers \App\Entity\User::setEnabled
     * @covers \App\Entity\User::isEnabled
     */
    public function testSetEnabled()
    {
        $this->booleanSetTest('enabled');
    }

    /**
     * @covers \App\Entity\User::setCampusId
     * @covers \App\Entity\User::getCampusId
     */
    public function testSetCampusId()
    {
        $this->basicSetTest('campusId', 'string');
    }

    /**
     * @covers \App\Entity\User::setOtherId
     * @covers \App\Entity\User::getOtherId
     */
    public function testSetOtherId()
    {
        $this->basicSetTest('otherId', 'string');
    }

    /**
     * @covers \App\Entity\User::setExamined
     * @covers \App\Entity\User::isExamined
     */
    public function testSetExamined()
    {
        $this->booleanSetTest('examined');
    }

    /**
     * @covers \App\Entity\User::setUserSyncIgnore
     * @covers \App\Entity\User::isUserSyncIgnore
     */
    public function testSetUserSyncIgnore()
    {
        $this->booleanSetTest('userSyncIgnore');
    }

    /**
     * @covers \App\Entity\User::setIcsFeedKey
     * @covers \App\Entity\User::generateIcsFeedKey
     */
    public function testSetIcsFeedKey()
    {
        $this->basicSetTest('icsFeedKey', 'string');
    }

    /**
     * @covers \App\Entity\User::setSchool
     * @covers \App\Entity\User::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\User::addAuditLog
     */
    public function testAddAuditLog()
    {
        $this->entityCollectionAddTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \App\Entity\User::removeAuditLog
     */
    public function testRemoveAuditLog()
    {
        $this->entityCollectionRemoveTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \App\Entity\User::setAuditLogs
     * @covers \App\Entity\User::getAuditLogs
     */
    public function testSetAuditLogs()
    {
        $this->entityCollectionSetTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \App\Entity\User::addDirectedCourse
     */
    public function testAddDirectedCourse()
    {
        $this->entityCollectionAddTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::removeDirectedCourse
     */
    public function testRemoveDirectedCourse()
    {
        $this->entityCollectionRemoveTest('directedCourse', 'Course', false, false, false, 'removeDirector');
    }

    /**
     * @covers \App\Entity\User::getDirectedCourses
     * @covers \App\Entity\User::setDirectedCourses
     */
    public function testGetDirectedCourses()
    {
        $this->entityCollectionSetTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::addAdministeredSession
     */
    public function testAddAdministeredSession()
    {
        $this->entityCollectionAddTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::removeAdministeredSession
     */
    public function testRemoveAdministeredSession()
    {
        $this->entityCollectionRemoveTest('administeredSession', 'Session', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \App\Entity\User::getAdministeredSessions
     * @covers \App\Entity\User::setAdministeredSessions
     */
    public function testGetAdministeredSessions()
    {
        $this->entityCollectionSetTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers \App\Entity\User::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup', false, false, false, 'removeUser');
    }

    /**
     * @covers \App\Entity\User::getLearnerGroups
     */
    public function testSetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers \App\Entity\User::addInstructedLearnerGroup
     */
    public function testAddInstructedLearnerGroup()
    {
        $this->entityCollectionAddTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::removeInstructedLearnerGroup
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
     * @covers \App\Entity\User::getInstructedLearnerGroups
     * @covers \App\Entity\User::setInstructedLearnerGroups
     */
    public function testGetInstructedLearnerGroups()
    {
        $this->entityCollectionSetTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers \App\Entity\User::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup', false, false, false, 'removeUser');
    }

    /**
     * @covers \App\Entity\User::getInstructorGroups
     */
    public function testSetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers \App\Entity\User::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers \App\Entity\User::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearner');
    }

    /**
     * @covers \App\Entity\User::getOfferings
     */
    public function testSetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers \App\Entity\User::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeDirector');
    }

    /**
     * @covers \App\Entity\User::getProgramYears
     */
    public function testSetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers \App\Entity\User::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeInstigator');
    }

    /**
     * @covers \App\Entity\User::getAlerts
     */
    public function testSetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers \App\Entity\User::addRole
     */
    public function testAddRole()
    {
        $this->entityCollectionAddTest('role', 'UserRole');
    }

    /**
     * @covers \App\Entity\User::removeRole
     */
    public function testRemoveRole()
    {
        $this->entityCollectionRemoveTest('role', 'UserRole');
    }

    /**
     * @covers \App\Entity\User::getRoles
     * @covers \App\Entity\User::setRoles
     */
    public function testSetRoles()
    {
        $this->entityCollectionSetTest('role', 'UserRole');
    }

    /**
     * @covers \App\Entity\User::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\User::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\User::getLearningMaterials
     */
    public function testSetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\User::addReport
     */
    public function testAddReport()
    {
        $this->entityCollectionAddTest('report', 'Report');
    }

    /**
     * @covers \App\Entity\User::removeReport
     */
    public function testRemoveReport()
    {
        $this->entityCollectionRemoveTest('report', 'Report');
    }

    /**
     * @covers \App\Entity\User::getReports
     * @covers \App\Entity\User::setReports
     */
    public function testSetReports()
    {
        $this->entityCollectionSetTest('report', 'Report');
    }

    /**
     * @covers \App\Entity\User::addCohort
     */
    public function testAddCohort()
    {
        $this->entityCollectionAddTest('cohort', 'Cohort');
    }

    /**
     * @covers \App\Entity\User::removeCohort
     */
    public function testRemoveCohort()
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort');
    }

    /**
     * @covers \App\Entity\User::getCohorts
     * @covers \App\Entity\User::setCohorts
     */
    public function testSetCohorts()
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock('App\Entity\Cohort');
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $obj2 = m::mock('App\Entity\Cohort');
        $this->object->setCohorts(new ArrayCollection([$obj2]));
        $this->assertNull($this->object->getPrimaryCohort());
    }

    /**
     * @covers \App\Entity\User::addInstructedOffering
     */
    public function testAddInstructedOffering()
    {
        $this->entityCollectionAddTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::removeInstructedOffering
     */
    public function testRemoveInstructedOffering()
    {
        $this->entityCollectionRemoveTest('instructedOffering', 'Offering', false, false, false, 'removeInstructor');
    }

    /**
     * @covers \App\Entity\User::getInstructedOfferings
     * @covers \App\Entity\User::setInstructedOfferings
     */
    public function testSetInstructedOffering()
    {
        $this->entityCollectionSetTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::addInstructorIlmSession
     */
    public function testAddInstructorIlmSessions()
    {
        $this->entityCollectionAddTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::removeInstructorIlmSession
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
     * @covers \App\Entity\User::getInstructorIlmSessions
     * @covers \App\Entity\User::setInstructorIlmSessions
     */
    public function testGetInstructorIlmSessions()
    {
        $this->entityCollectionSetTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::addLearnerIlmSession
     */
    public function testAddLearnerIlmSessions()
    {
        $this->entityCollectionAddTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

    /**
     * @covers \App\Entity\User::removeLearnerIlmSession
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
     * @covers \App\Entity\User::getLearnerIlmSessions
     * @covers \App\Entity\User::setLearnerIlmSessions
     */
    public function testGetLearnerIlmSessions()
    {
        $this->entityCollectionSetTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

    /**
     * @covers \App\Entity\User::setPrimaryCohort
     * @covers \App\Entity\User::getPrimaryCohort
     */
    public function testSetPrimaryCohort()
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock('App\Entity\Cohort');
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $this->assertSame($obj, $this->object->getPrimaryCohort());
        $this->assertTrue($this->object->getCohorts()->contains($obj));
    }

    /**
     * @covers \App\Entity\User::addPendingUserUpdate
     */
    public function testAddPendingUserUpdates()
    {
        $this->entityCollectionAddTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \App\Entity\User::removePendingUserUpdate
     */
    public function testRemovePendingUserUpdates()
    {
        $this->entityCollectionRemoveTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \App\Entity\User::getPendingUserUpdates
     * @covers \App\Entity\User::setPendingUserUpdates
     */
    public function testGetPendingUserUpdates()
    {
        $this->entityCollectionSetTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \App\Entity\User::addDirectedSchool
     */
    public function testAddDirectedSchool()
    {
        $this->entityCollectionAddTest('directedSchool', 'School', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::removeDirectedSchool
     */
    public function testRemoveDirectedSchool()
    {
        $this->entityCollectionRemoveTest('directedSchool', 'School', false, false, false, 'removeDirector');
    }

    /**
     * @covers \App\Entity\User::getDirectedSchools
     * @covers \App\Entity\User::setDirectedSchools
     */
    public function testGetDirectedSchools()
    {
        $this->entityCollectionSetTest('directedSchool', 'School', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::addAdministeredCourse
     */
    public function testAddAdministeredCourse()
    {
        $this->entityCollectionAddTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::removeAdministeredCourse
     */
    public function testRemoveAdministeredCourse()
    {
        $this->entityCollectionRemoveTest('administeredCourse', 'Course', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \App\Entity\User::getAdministeredCourses
     * @covers \App\Entity\User::setAdministeredCourses
     */
    public function testGetAdministeredCourses()
    {
        $this->entityCollectionSetTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::addAdministeredSchool
     */
    public function testAddAdministeredSchool()
    {
        $this->entityCollectionAddTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::removeAdministeredSchool
     */
    public function testRemoveAdministeredSchool()
    {
        $this->entityCollectionRemoveTest('administeredSchool', 'School', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \App\Entity\User::getAdministeredSchools
     * @covers \App\Entity\User::setAdministeredSchools
     */
    public function testGetAdministeredSchools()
    {
        $this->entityCollectionSetTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::addDirectedProgram
     */
    public function testAddDirectedProgram()
    {
        $this->entityCollectionAddTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::removeDirectedProgram
     */
    public function testRemoveDirectedProgram()
    {
        $this->entityCollectionRemoveTest('directedProgram', 'Program', false, false, false, 'removeDirector');
    }

    /**
     * @covers \App\Entity\User::getDirectedPrograms
     * @covers \App\Entity\User::setDirectedPrograms
     */
    public function testGetDirectedPrograms()
    {
        $this->entityCollectionSetTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::isRoot
     * @covers \App\Entity\User::setRoot
     */
    public function testIsRoot()
    {
        $this->booleanSetTest('root');
    }

    /**
     * @covers \App\Entity\User::setAuthentication()
     * @covers \App\Entity\User::getAuthentication()
     */
    public function testSetAuthentication()
    {
        $this->assertTrue(method_exists($this->object, 'getAuthentication'), "Method getAuthentication missing");
        $this->assertTrue(method_exists($this->object, 'setAuthentication'), "Method setAuthentication missing");
        $obj = m::mock('App\Entity\Authentication');
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
    }

    /**
     * @covers \App\Entity\User::setAuthentication()
     */
    public function testSetAuthenticationNull()
    {
        $obj = m::mock('App\Entity\Authentication');
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
        $this->object->setAuthentication(null);
        $this->assertSame(null, $this->object->getAuthentication());
    }

    /**
     * @covers \App\Entity\User::addAdministeredCurriculumInventoryReport
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
     * @covers \App\Entity\User::removeAdministeredCurriculumInventoryReport
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
     * @covers \App\Entity\User::getAdministeredCurriculumInventoryReports
     * @covers \App\Entity\User::setAdministeredCurriculumInventoryReports
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
