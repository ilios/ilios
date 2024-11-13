<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Cohort;
use App\Entity\Authentication;
use App\Entity\User;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for Entity Objective
 * @group model
 */
class UserTest extends EntityBase
{
    protected User $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new User();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'lastName',
            'firstName',
            'email',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setLastName('Andrews');
        $this->object->setFirstName('Julia');
        $this->object->setEmail('sanders@ucsf.edu');
        $this->object->setMiddleName('');
        $this->object->setDisplayName('');
        $this->object->setPhone('');
        $this->object->setPreferredEmail('');
        $this->object->setCampusId('');
        $this->object->setOtherId('');
        $this->validate(0);
        $this->object->setMiddleName('test');
        $this->object->setDisplayName('test');
        $this->object->setPhone('test');
        $this->object->setPreferredEmail('test@example.com');
        $this->object->setCampusId('test');
        $this->object->setOtherId('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\User::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getAlerts());
        $this->assertCount(0, $this->object->getDirectedCourses());
        $this->assertCount(0, $this->object->getInstructorGroups());
        $this->assertCount(0, $this->object->getInstructedLearnerGroups());
        $this->assertCount(0, $this->object->getOfferings());
        $this->assertCount(0, $this->object->getProgramYears());
        $this->assertCount(0, $this->object->getRoles());
        $this->assertCount(0, $this->object->getLearnerGroups());
        $this->assertCount(0, $this->object->getLearningMaterials());
        $this->assertCount(0, $this->object->getCohorts());
        $this->assertCount(0, $this->object->getAuditLogs());
        $this->assertCount(0, $this->object->getReports());
        $this->assertCount(0, $this->object->getPendingUserUpdates());
        $this->assertCount(0, $this->object->getAdministeredSessions());
        $this->assertCount(0, $this->object->getAdministeredCourses());
        $this->assertCount(0, $this->object->getStudentAdvisedCourses());
        $this->assertCount(0, $this->object->getStudentAdvisedSessions());
        $this->assertCount(0, $this->object->getDirectedSchools());
        $this->assertCount(0, $this->object->getAdministeredSchools());
        $this->assertCount(0, $this->object->getDirectedPrograms());
        $this->assertFalse($this->object->isRoot());
    }

    /**
     * @covers \App\Entity\User::setLastName
     * @covers \App\Entity\User::getLastName
     */
    public function testSetLastName(): void
    {
        $this->basicSetTest('lastName', 'string');
    }

    /**
     * @covers \App\Entity\User::setFirstName
     * @covers \App\Entity\User::getFirstName
     */
    public function testSetFirstName(): void
    {
        $this->basicSetTest('firstName', 'string');
    }

    /**
     * @covers \App\Entity\User::setMiddleName
     * @covers \App\Entity\User::getMiddleName
     */
    public function testSetMiddleName(): void
    {
        $this->basicSetTest('middleName', 'string');
    }

    /**
     * @covers \App\Entity\User::setDisplayName
     * @covers \App\Entity\User::getDisplayName
     */
    public function testSetDisplayName(): void
    {
        $this->basicSetTest('displayName', 'string');
    }

    /**
     * @covers \App\Entity\User::setPhone
     * @covers \App\Entity\User::getPhone
     */
    public function testSetPhone(): void
    {
        $this->basicSetTest('phone', 'phone');
    }

    /**
     * @covers \App\Entity\User::setEmail
     * @covers \App\Entity\User::getEmail
     */
    public function testSetEmail(): void
    {
        $this->basicSetTest('email', 'email');
    }

    /**
     * @covers \App\Entity\User::setPreferredEmail
     * @covers \App\Entity\User::getPreferredEmail
     */
    public function testSetPreferredEmail(): void
    {
        $this->basicSetTest('preferredEmail', 'email');
    }

    /**
     * @covers \App\Entity\User::setPronouns
     * @covers \App\Entity\User::getPronouns
     */
    public function testSetPronouns(): void
    {
        $this->basicSetTest('pronouns', 'string');
    }

    /**
     * @covers \App\Entity\User::setAddedViaIlios
     * @covers \App\Entity\User::isAddedViaIlios
     */
    public function testSetAddedViaIlios(): void
    {
        $this->booleanSetTest('addedViaIlios');
    }

    /**
     * @covers \App\Entity\User::setEnabled
     * @covers \App\Entity\User::isEnabled
     */
    public function testSetEnabled(): void
    {
        $this->booleanSetTest('enabled');
    }

    /**
     * @covers \App\Entity\User::setCampusId
     * @covers \App\Entity\User::getCampusId
     */
    public function testSetCampusId(): void
    {
        $this->basicSetTest('campusId', 'string');
    }

    /**
     * @covers \App\Entity\User::setOtherId
     * @covers \App\Entity\User::getOtherId
     */
    public function testSetOtherId(): void
    {
        $this->basicSetTest('otherId', 'string');
    }

    /**
     * @covers \App\Entity\User::setExamined
     * @covers \App\Entity\User::isExamined
     */
    public function testSetExamined(): void
    {
        $this->booleanSetTest('examined');
    }

    /**
     * @covers \App\Entity\User::setUserSyncIgnore
     * @covers \App\Entity\User::isUserSyncIgnore
     */
    public function testSetUserSyncIgnore(): void
    {
        $this->booleanSetTest('userSyncIgnore');
    }

    /**
     * @covers \App\Entity\User::setIcsFeedKey
     * @covers \App\Entity\User::generateIcsFeedKey
     */
    public function testSetIcsFeedKey(): void
    {
        $this->basicSetTest('icsFeedKey', 'string');
    }

    /**
     * @covers \App\Entity\User::setSchool
     * @covers \App\Entity\User::getSchool
     */
    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\User::addAuditLog
     */
    public function testAddAuditLog(): void
    {
        $this->entityCollectionAddTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \App\Entity\User::removeAuditLog
     */
    public function testRemoveAuditLog(): void
    {
        $this->entityCollectionRemoveTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \App\Entity\User::setAuditLogs
     * @covers \App\Entity\User::getAuditLogs
     */
    public function testSetAuditLogs(): void
    {
        $this->entityCollectionSetTest('auditLog', 'AuditLog');
    }

    /**
     * @covers \App\Entity\User::addDirectedCourse
     */
    public function testAddDirectedCourse(): void
    {
        $this->entityCollectionAddTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::removeDirectedCourse
     */
    public function testRemoveDirectedCourse(): void
    {
        $this->entityCollectionRemoveTest('directedCourse', 'Course', false, false, false, 'removeDirector');
    }

    /**
     * @covers \App\Entity\User::getDirectedCourses
     * @covers \App\Entity\User::setDirectedCourses
     */
    public function testGetDirectedCourses(): void
    {
        $this->entityCollectionSetTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::addStudentAdvisedCourse
     */
    public function testAddStudentAdvisedCourse(): void
    {
        $this->entityCollectionAddTest('studentAdvisedCourse', 'Course', false, false, 'addStudentAdvisor');
    }

    /**
     * @covers \App\Entity\User::removeStudentAdvisedCourse
     */
    public function testRemoveStudentAdvisedCourse(): void
    {
        $this->entityCollectionRemoveTest(
            'studentAdvisedCourse',
            'Course',
            false,
            false,
            false,
            'removeStudentAdvisor'
        );
    }

    /**
     * @covers \App\Entity\User::getStudentAdvisedCourses
     * @covers \App\Entity\User::setStudentAdvisedCourses
     */
    public function testGetStudentAdvisedCourses(): void
    {
        $this->entityCollectionSetTest('studentAdvisedCourse', 'Course', false, false, 'addStudentAdvisor');
    }

    /**
     * @covers \App\Entity\User::addAdministeredSession
     */
    public function testAddAdministeredSession(): void
    {
        $this->entityCollectionAddTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::removeAdministeredSession
     */
    public function testRemoveAdministeredSession(): void
    {
        $this->entityCollectionRemoveTest('administeredSession', 'Session', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \App\Entity\User::getAdministeredSessions
     * @covers \App\Entity\User::setAdministeredSessions
     */
    public function testGetAdministeredSessions(): void
    {
        $this->entityCollectionSetTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::addStudentAdvisedSession
     */
    public function testAddStudentAdvisedSession(): void
    {
        $this->entityCollectionAddTest('studentAdvisedSession', 'Session', false, false, 'addStudentAdvisor');
    }

    /**
     * @covers \App\Entity\User::removeStudentAdvisedSession
     */
    public function testRemoveStudentAdvisedSession(): void
    {
        $this->entityCollectionRemoveTest(
            'studentAdvisedSession',
            'Session',
            false,
            false,
            false,
            'removeStudentAdvisor'
        );
    }

    /**
     * @covers \App\Entity\User::getStudentAdvisedSessions
     * @covers \App\Entity\User::setStudentAdvisedSessions
     */
    public function testGetStudentAdvisedSessions(): void
    {
        $this->entityCollectionSetTest('studentAdvisedSession', 'Session', false, false, 'addStudentAdvisor');
    }

    /**
     * @covers \App\Entity\User::addLearnerGroup
     */
    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers \App\Entity\User::removeLearnerGroup
     */
    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup', false, false, false, 'removeUser');
    }

    /**
     * @covers \App\Entity\User::getLearnerGroups
     */
    public function testSetLearnerGroups(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers \App\Entity\User::addInstructedLearnerGroup
     */
    public function testAddInstructedLearnerGroup(): void
    {
        $this->entityCollectionAddTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::removeInstructedLearnerGroup
     */
    public function testRemoveInstructedLearnerGroup(): void
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
    public function testGetInstructedLearnerGroups(): void
    {
        $this->entityCollectionSetTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::addInstructorGroup
     */
    public function testAddInstructorGroup(): void
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers \App\Entity\User::removeInstructorGroup
     */
    public function testRemoveInstructorGroup(): void
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup', false, false, false, 'removeUser');
    }

    /**
     * @covers \App\Entity\User::getInstructorGroups
     */
    public function testSetInstructorGroups(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers \App\Entity\User::addOffering
     */
    public function testAddOffering(): void
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers \App\Entity\User::removeOffering
     */
    public function testRemoveOffering(): void
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearner');
    }

    /**
     * @covers \App\Entity\User::getOfferings
     */
    public function testSetOfferings(): void
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers \App\Entity\User::addProgramYear
     */
    public function testAddProgramYear(): void
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::removeProgramYear
     */
    public function testRemoveProgramYear(): void
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeDirector');
    }

    /**
     * @covers \App\Entity\User::getProgramYears
     */
    public function testSetProgramYears(): void
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::addAlert
     */
    public function testAddAlert(): void
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers \App\Entity\User::removeAlert
     */
    public function testRemoveAlert(): void
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeInstigator');
    }

    /**
     * @covers \App\Entity\User::getAlerts
     */
    public function testSetAlerts(): void
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers \App\Entity\User::addRole
     */
    public function testAddRole(): void
    {
        $this->entityCollectionAddTest('role', 'UserRole');
    }

    /**
     * @covers \App\Entity\User::removeRole
     */
    public function testRemoveRole(): void
    {
        $this->entityCollectionRemoveTest('role', 'UserRole');
    }

    /**
     * @covers \App\Entity\User::getRoles
     * @covers \App\Entity\User::setRoles
     */
    public function testSetRoles(): void
    {
        $this->entityCollectionSetTest('role', 'UserRole');
    }

    /**
     * @covers \App\Entity\User::addLearningMaterial
     */
    public function testAddLearningMaterial(): void
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\User::removeLearningMaterial
     */
    public function testRemoveLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\User::getLearningMaterials
     */
    public function testSetLearningMaterials(): void
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\User::addReport
     */
    public function testAddReport(): void
    {
        $this->entityCollectionAddTest('report', 'Report');
    }

    /**
     * @covers \App\Entity\User::removeReport
     */
    public function testRemoveReport(): void
    {
        $this->entityCollectionRemoveTest('report', 'Report');
    }

    /**
     * @covers \App\Entity\User::getReports
     * @covers \App\Entity\User::setReports
     */
    public function testSetReports(): void
    {
        $this->entityCollectionSetTest('report', 'Report');
    }

    /**
     * @covers \App\Entity\User::addCohort
     */
    public function testAddCohort(): void
    {
        $this->entityCollectionAddTest('cohort', 'Cohort');
    }

    /**
     * @covers \App\Entity\User::removeCohort
     */
    public function testRemoveCohort(): void
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort');
    }

    /**
     * @covers \App\Entity\User::getCohorts
     * @covers \App\Entity\User::setCohorts
     */
    public function testSetCohorts(): void
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock(Cohort::class);
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $obj2 = m::mock(Cohort::class);
        $this->object->setCohorts(new ArrayCollection([$obj2]));
        $this->assertNull($this->object->getPrimaryCohort());
    }

    /**
     * @covers \App\Entity\User::addInstructedOffering
     */
    public function testAddInstructedOffering(): void
    {
        $this->entityCollectionAddTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::removeInstructedOffering
     */
    public function testRemoveInstructedOffering(): void
    {
        $this->entityCollectionRemoveTest('instructedOffering', 'Offering', false, false, false, 'removeInstructor');
    }

    /**
     * @covers \App\Entity\User::getInstructedOfferings
     * @covers \App\Entity\User::setInstructedOfferings
     */
    public function testSetInstructedOffering(): void
    {
        $this->entityCollectionSetTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::addInstructorIlmSession
     */
    public function testAddInstructorIlmSessions(): void
    {
        $this->entityCollectionAddTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::removeInstructorIlmSession
     */
    public function testRemoveInstructorIlmSessions(): void
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
    public function testGetInstructorIlmSessions(): void
    {
        $this->entityCollectionSetTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers \App\Entity\User::addLearnerIlmSession
     */
    public function testAddLearnerIlmSessions(): void
    {
        $this->entityCollectionAddTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

    /**
     * @covers \App\Entity\User::removeLearnerIlmSession
     */
    public function testRemoveLearnerIlmSessions(): void
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
    public function testGetLearnerIlmSessions(): void
    {
        $this->entityCollectionSetTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

    /**
     * @covers \App\Entity\User::setPrimaryCohort
     * @covers \App\Entity\User::getPrimaryCohort
     */
    public function testSetPrimaryCohort(): void
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock(Cohort::class);
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $this->assertSame($obj, $this->object->getPrimaryCohort());
        $this->assertTrue($this->object->getCohorts()->contains($obj));
    }

    /**
     * @covers \App\Entity\User::addPendingUserUpdate
     */
    public function testAddPendingUserUpdates(): void
    {
        $this->entityCollectionAddTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \App\Entity\User::removePendingUserUpdate
     */
    public function testRemovePendingUserUpdates(): void
    {
        $this->entityCollectionRemoveTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \App\Entity\User::getPendingUserUpdates
     * @covers \App\Entity\User::setPendingUserUpdates
     */
    public function testGetPendingUserUpdates(): void
    {
        $this->entityCollectionSetTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    /**
     * @covers \App\Entity\User::addDirectedSchool
     */
    public function testAddDirectedSchool(): void
    {
        $this->entityCollectionAddTest('directedSchool', 'School', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::removeDirectedSchool
     */
    public function testRemoveDirectedSchool(): void
    {
        $this->entityCollectionRemoveTest('directedSchool', 'School', false, false, false, 'removeDirector');
    }

    /**
     * @covers \App\Entity\User::getDirectedSchools
     * @covers \App\Entity\User::setDirectedSchools
     */
    public function testGetDirectedSchools(): void
    {
        $this->entityCollectionSetTest('directedSchool', 'School', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::addAdministeredCourse
     */
    public function testAddAdministeredCourse(): void
    {
        $this->entityCollectionAddTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::removeAdministeredCourse
     */
    public function testRemoveAdministeredCourse(): void
    {
        $this->entityCollectionRemoveTest('administeredCourse', 'Course', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \App\Entity\User::getAdministeredCourses
     * @covers \App\Entity\User::setAdministeredCourses
     */
    public function testGetAdministeredCourses(): void
    {
        $this->entityCollectionSetTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::addAdministeredSchool
     */
    public function testAddAdministeredSchool(): void
    {
        $this->entityCollectionAddTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::removeAdministeredSchool
     */
    public function testRemoveAdministeredSchool(): void
    {
        $this->entityCollectionRemoveTest('administeredSchool', 'School', false, false, false, 'removeAdministrator');
    }

    /**
     * @covers \App\Entity\User::getAdministeredSchools
     * @covers \App\Entity\User::setAdministeredSchools
     */
    public function testGetAdministeredSchools(): void
    {
        $this->entityCollectionSetTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    /**
     * @covers \App\Entity\User::addDirectedProgram
     */
    public function testAddDirectedProgram(): void
    {
        $this->entityCollectionAddTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::removeDirectedProgram
     */
    public function testRemoveDirectedProgram(): void
    {
        $this->entityCollectionRemoveTest('directedProgram', 'Program', false, false, false, 'removeDirector');
    }

    /**
     * @covers \App\Entity\User::getDirectedPrograms
     * @covers \App\Entity\User::setDirectedPrograms
     */
    public function testGetDirectedPrograms(): void
    {
        $this->entityCollectionSetTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    /**
     * @covers \App\Entity\User::isRoot
     * @covers \App\Entity\User::setRoot
     */
    public function testIsRoot(): void
    {
        $this->booleanSetTest('root');
    }

    /**
     * @covers \App\Entity\User::setAuthentication()
     * @covers \App\Entity\User::getAuthentication()
     */
    public function testSetAuthentication(): void
    {
        $this->assertTrue(method_exists($this->object, 'getAuthentication'), "Method getAuthentication missing");
        $this->assertTrue(method_exists($this->object, 'setAuthentication'), "Method setAuthentication missing");
        $obj = m::mock(Authentication::class);
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
    }

    /**
     * @covers \App\Entity\User::setAuthentication()
     */
    public function testSetAuthenticationNull(): void
    {
        $obj = m::mock(Authentication::class);
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
        $this->object->setAuthentication(null);
        $this->assertSame(null, $this->object->getAuthentication());
    }

    /**
     * @covers \App\Entity\User::addAdministeredCurriculumInventoryReport
     */
    public function testAddAdministeredCurriculumInventoryReport(): void
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
    public function testRemoveAdministeredCurriculumInventoryReport(): void
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
    public function testGetAdministeredCurriculumInventoryReports(): void
    {
        $this->entityCollectionSetTest(
            'administeredCurriculumInventoryReport',
            'CurriculumInventoryReport',
            false,
            false,
            'addAdministrator'
        );
    }

    /**
     * @covers \App\Entity\User::addSessionMaterialStatus
     */
    public function testAddSessionMaterialStatus(): void
    {
        $this->entityCollectionAddTest(
            'sessionMaterialStatus',
            'UserSessionMaterialStatus',
            'getSessionMaterialStatuses',
            'addSessionMaterialStatus'
        );
    }

    /**
     * @covers \App\Entity\User::removeSessionMaterialStatus
     */
    public function testRemoveLearningMaterialStatus(): void
    {
        $this->entityCollectionRemoveTest(
            'sessionMaterialStatus',
            'UserSessionMaterialStatus',
            'getSessionMaterialStatuses',
            'addSessionMaterialStatus',
            'removeSessionMaterialStatus'
        );
    }

    /**
     * @covers \App\Entity\User::getSessionMaterialStatuses
     * @covers \App\Entity\User::setSessionMaterialStatuses
     */
    public function testGetSessionLearningMaterialStatuses(): void
    {
        $this->entityCollectionSetTest(
            'sessionMaterialStatus',
            'UserSessionMaterialStatus',
            'getSessionMaterialStatuses',
            'setSessionMaterialStatuses'
        );
    }

    protected function getObject(): User
    {
        return $this->object;
    }
}
