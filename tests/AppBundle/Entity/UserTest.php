<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
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
     * @covers \AppBundle\Entity\User::__construct
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
     * @covers \AppBundle\Entity\User::setLastName
     * @covers \AppBundle\Entity\User::getLastName
     */
    public function testSetLastName()
    {
        $this->basicSetTest('lastName', 'string');
    }

    /**
     * @covers \AppBundle\Entity\User::setFirstName
     * @covers \AppBundle\Entity\User::getFirstName
     */
    public function testSetFirstName()
    {
        $this->basicSetTest('firstName', 'string');
    }

    /**
     * @covers \AppBundle\Entity\User::setMiddleName
     * @covers \AppBundle\Entity\User::getMiddleName
     */
    public function testSetMiddleName()
    {
        $this->basicSetTest('middleName', 'string');
    }

    /**
     * @covers \AppBundle\Entity\User::setPhone
     * @covers \AppBundle\Entity\User::getPhone
     */
    public function testSetPhone()
    {
        $this->basicSetTest('phone', 'phone');
    }

    /**
     * @covers \AppBundle\Entity\User::setEmail
     * @covers \AppBundle\Entity\User::getEmail
     */
    public function testSetEmail()
    {
        $this->basicSetTest('email', 'email');
    }

    /**
     * @covers \AppBundle\Entity\User::setAddedViaIlios
     * @covers \AppBundle\Entity\User::isAddedViaIlios
     */
    public function testSetAddedViaIlios()
    {
        $this->booleanSetTest('addedViaIlios');
    }

    /**
     * @covers \AppBundle\Entity\User::setEnabled
     * @covers \AppBundle\Entity\User::isEnabled
     */
    public function testSetEnabled()
    {
        $this->booleanSetTest('enabled');
    }

    /**
     * @covers \AppBundle\Entity\User::setCampusId
     * @covers \AppBundle\Entity\User::getCampusId
     */
    public function testSetCampusId()
    {
        $this->basicSetTest('campusId', 'string');
    }

    /**
     * @covers \AppBundle\Entity\User::setOtherId
     * @covers \AppBundle\Entity\User::getOtherId
     */
    public function testSetOtherId()
    {
        $this->basicSetTest('otherId', 'string');
    }

    /**
     * @covers \AppBundle\Entity\User::setExamined
     * @covers \AppBundle\Entity\User::isExamined
     */
    public function testSetExamined()
    {
        $this->booleanSetTest('examined');
    }

    /**
     * @covers \AppBundle\Entity\User::setUserSyncIgnore
     * @covers \AppBundle\Entity\User::isUserSyncIgnore
     */
    public function testSetUserSyncIgnore()
    {
        $this->booleanSetTest('userSyncIgnore');
    }

    /**
     * @covers \AppBundle\Entity\User::setIcsFeedKey
     * @covers \AppBundle\Entity\User::generateIcsFeedKey
     */
    public function testSetIcsFeedKey()
    {
        $this->basicSetTest('icsFeedKey', 'string');
    }

    /**
     * @covers \AppBundle\Entity\User::setSchool
     * @covers \AppBundle\Entity\User::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \AppBundle\Entity\User::addAuditLog
     */
    public function testAddAuditLog()
    {
        $this->entityCollectionAddTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \AppBundle\Entity\User::removeAuditLog
     */
    public function testRemoveAuditLog()
    {
        $this->entityCollectionRemoveTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \AppBundle\Entity\User::setAuditLogs
     * @covers \AppBundle\Entity\User::getAuditLogs
     */
    public function testSetAuditLogs()
    {
        $this->entityCollectionSetTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \AppBundle\Entity\User::addDirectedCourse
     */
    public function testAddDirectedCourse()
    {
        $this->entityCollectionAddTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::removeDirectedCourse
     */
    public function testRemoveDirectedCourse()
    {
        $this->entityCollectionRemoveTest('directedCourse', 'Course', false, false, false, 'removeDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::getDirectedCourses
     * @covers \AppBundle\Entity\User::setDirectedCourses
     */
    public function testGetDirectedCourses()
    {
        $this->entityCollectionSetTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::addAdministeredSession
     */
    public function testAddAdministeredSession()
    {
        $this->entityCollectionAddTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    /**
     * @covers \AppBundle\Entity\User::removeAdministeredSession
     */
    public function testRemoveAdministeredSession()
    {
        $this->entityCollectionRemoveTest('administeredSession', 'Session', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \AppBundle\Entity\User::getAdministeredSessions
     * @covers \AppBundle\Entity\User::setAdministeredSessions
     */
    public function testGetAdministeredSessions()
    {
        $this->entityCollectionSetTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    /**
     * @covers \AppBundle\Entity\User::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers \AppBundle\Entity\User::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup', false, false, false, 'removeUser');
    }

    /**
     * @covers \AppBundle\Entity\User::getLearnerGroups
     */
    public function testSetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers \AppBundle\Entity\User::addInstructedLearnerGroup
     */
    public function testAddInstructedLearnerGroup()
    {
        $this->entityCollectionAddTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers \AppBundle\Entity\User::removeInstructedLearnerGroup
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
     * @covers \AppBundle\Entity\User::getInstructedLearnerGroups
     * @covers \AppBundle\Entity\User::setInstructedLearnerGroups
     */
    public function testGetInstructedLearnerGroups()
    {
        $this->entityCollectionSetTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers \AppBundle\Entity\User::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers \AppBundle\Entity\User::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup', false, false, false, 'removeUser');
    }

    /**
     * @covers \AppBundle\Entity\User::getInstructorGroups
     */
    public function testSetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers \AppBundle\Entity\User::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers \AppBundle\Entity\User::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearner');
    }

    /**
     * @covers \AppBundle\Entity\User::getOfferings
     */
    public function testSetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers \AppBundle\Entity\User::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::getProgramYears
     */
    public function testSetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers \AppBundle\Entity\User::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeInstigator');
    }

    /**
     * @covers \AppBundle\Entity\User::getAlerts
     */
    public function testSetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers \AppBundle\Entity\User::addRole
     */
    public function testAddRole()
    {
        $this->entityCollectionAddTest('role', 'UserRole');
    }

    /**
     * @covers \AppBundle\Entity\User::removeRole
     */
    public function testRemoveRole()
    {
        $this->entityCollectionRemoveTest('role', 'UserRole');
    }

    /**
     * @covers \AppBundle\Entity\User::getRoles
     * @covers \AppBundle\Entity\User::setRoles
     */
    public function testSetRoles()
    {
        $this->entityCollectionSetTest('role', 'UserRole');
    }

    /**
     * @covers \AppBundle\Entity\User::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\User::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\User::getLearningMaterials
     */
    public function testSetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\User::addReport
     */
    public function testAddReport()
    {
        $this->entityCollectionAddTest('report', 'Report');
    }

    /**
     * @covers \AppBundle\Entity\User::removeReport
     */
    public function testRemoveReport()
    {
        $this->entityCollectionRemoveTest('report', 'Report');
    }

    /**
     * @covers \AppBundle\Entity\User::getReports
     * @covers \AppBundle\Entity\User::setReports
     */
    public function testSetReports()
    {
        $this->entityCollectionSetTest('report', 'Report');
    }

    /**
     * @covers \AppBundle\Entity\User::addCohort
     */
    public function testAddCohort()
    {
        $this->entityCollectionAddTest('cohort', 'Cohort');
    }

    /**
     * @covers \AppBundle\Entity\User::removeCohort
     */
    public function testRemoveCohort()
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort');
    }

    /**
     * @covers \AppBundle\Entity\User::getCohorts
     * @covers \AppBundle\Entity\User::setCohorts
     */
    public function testSetCohorts()
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock('AppBundle\Entity\Cohort');
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $obj2 = m::mock('AppBundle\Entity\Cohort');
        $this->object->setCohorts(new ArrayCollection([$obj2]));
        $this->assertNull($this->object->getPrimaryCohort());
    }

    /**
     * @covers \AppBundle\Entity\User::addInstructedOffering
     */
    public function testAddInstructedOffering()
    {
        $this->entityCollectionAddTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers \AppBundle\Entity\User::removeInstructedOffering
     */
    public function testRemoveInstructedOffering()
    {
        $this->entityCollectionRemoveTest('instructedOffering', 'Offering', false, false, false, 'removeInstructor');
    }

    /**
     * @covers \AppBundle\Entity\User::getInstructedOfferings
     * @covers \AppBundle\Entity\User::setInstructedOfferings
     */
    public function testSetInstructedOffering()
    {
        $this->entityCollectionSetTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers \AppBundle\Entity\User::addInstructorIlmSession
     */
    public function testAddInstructorIlmSessions()
    {
        $this->entityCollectionAddTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers \AppBundle\Entity\User::removeInstructorIlmSession
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
     * @covers \AppBundle\Entity\User::getInstructorIlmSessions
     * @covers \AppBundle\Entity\User::setInstructorIlmSessions
     */
    public function testGetInstructorIlmSessions()
    {
        $this->entityCollectionSetTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers \AppBundle\Entity\User::addLearnerIlmSession
     */
    public function testAddLearnerIlmSessions()
    {
        $this->entityCollectionAddTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

    /**
     * @covers \AppBundle\Entity\User::removeLearnerIlmSession
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
     * @covers \AppBundle\Entity\User::getLearnerIlmSessions
     * @covers \AppBundle\Entity\User::setLearnerIlmSessions
     */
    public function testGetLearnerIlmSessions()
    {
        $this->entityCollectionSetTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

    /**
     * @covers \AppBundle\Entity\User::setPrimaryCohort
     * @covers \AppBundle\Entity\User::getPrimaryCohort
     */
    public function testSetPrimaryCohort()
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock('AppBundle\Entity\Cohort');
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $this->assertSame($obj, $this->object->getPrimaryCohort());
        $this->assertTrue($this->object->getCohorts()->contains($obj));
    }

    /**
     * @covers \AppBundle\Entity\User::addPendingUserUpdate
     */
    public function testAddPendingUserUpdates()
    {
        $this->entityCollectionAddTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \AppBundle\Entity\User::removePendingUserUpdate
     */
    public function testRemovePendingUserUpdates()
    {
        $this->entityCollectionRemoveTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \AppBundle\Entity\User::getPendingUserUpdates
     * @covers \AppBundle\Entity\User::setPendingUserUpdates
     */
    public function testGetPendingUserUpdates()
    {
        $this->entityCollectionSetTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \AppBundle\Entity\User::addDirectedSchool
     */
    public function testAddDirectedSchool()
    {
        $this->entityCollectionAddTest('directedSchool', 'School', false, false, 'addDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::removeDirectedSchool
     */
    public function testRemoveDirectedSchool()
    {
        $this->entityCollectionRemoveTest('directedSchool', 'School', false, false, false, 'removeDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::getDirectedSchools
     * @covers \AppBundle\Entity\User::setDirectedSchools
     */
    public function testGetDirectedSchools()
    {
        $this->entityCollectionSetTest('directedSchool', 'School', false, false, 'addDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::addAdministeredCourse
     */
    public function testAddAdministeredCourse()
    {
        $this->entityCollectionAddTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    /**
     * @covers \AppBundle\Entity\User::removeAdministeredCourse
     */
    public function testRemoveAdministeredCourse()
    {
        $this->entityCollectionRemoveTest('administeredCourse', 'Course', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \AppBundle\Entity\User::getAdministeredCourses
     * @covers \AppBundle\Entity\User::setAdministeredCourses
     */
    public function testGetAdministeredCourses()
    {
        $this->entityCollectionSetTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    /**
     * @covers \AppBundle\Entity\User::addAdministeredSchool
     */
    public function testAddAdministeredSchool()
    {
        $this->entityCollectionAddTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    /**
     * @covers \AppBundle\Entity\User::removeAdministeredSchool
     */
    public function testRemoveAdministeredSchool()
    {
        $this->entityCollectionRemoveTest('administeredSchool', 'School', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \AppBundle\Entity\User::getAdministeredSchools
     * @covers \AppBundle\Entity\User::setAdministeredSchools
     */
    public function testGetAdministeredSchools()
    {
        $this->entityCollectionSetTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    /**
     * @covers \AppBundle\Entity\User::addDirectedProgram
     */
    public function testAddDirectedProgram()
    {
        $this->entityCollectionAddTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::removeDirectedProgram
     */
    public function testRemoveDirectedProgram()
    {
        $this->entityCollectionRemoveTest('directedProgram', 'Program', false, false, false, 'removeDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::getDirectedPrograms
     * @covers \AppBundle\Entity\User::setDirectedPrograms
     */
    public function testGetDirectedPrograms()
    {
        $this->entityCollectionSetTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    /**
     * @covers \AppBundle\Entity\User::isRoot
     * @covers \AppBundle\Entity\User::setRoot
     */
    public function testIsRoot()
    {
        $this->booleanSetTest('root');
    }

    /**
     * @covers \AppBundle\Entity\User::setAuthentication()
     * @covers \AppBundle\Entity\User::getAuthentication()
     */
    public function testSetAuthentication()
    {
        $this->assertTrue(method_exists($this->object, 'getAuthentication'), "Method getAuthentication missing");
        $this->assertTrue(method_exists($this->object, 'setAuthentication'), "Method setAuthentication missing");
        $obj = m::mock('AppBundle\Entity\Authentication');
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
    }

    /**
     * @covers \AppBundle\Entity\User::setAuthentication()
     */
    public function testSetAuthenticationNull()
    {
        $obj = m::mock('AppBundle\Entity\Authentication');
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
        $this->object->setAuthentication(null);
        $this->assertSame(null, $this->object->getAuthentication());
    }

    /**
     * @covers \AppBundle\Entity\User::addAdministeredCurriculumInventoryReport
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
     * @covers \AppBundle\Entity\User::removeAdministeredCurriculumInventoryReport
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
     * @covers \AppBundle\Entity\User::getAdministeredCurriculumInventoryReports
     * @covers \AppBundle\Entity\User::setAdministeredCurriculumInventoryReports
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
