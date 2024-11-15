<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\Cohort;
use App\Entity\Authentication;
use App\Entity\User;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for Entity Objective
 */
#[Group('model')]
#[CoversClass(User::class)]
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

    public function testSetLastName(): void
    {
        $this->basicSetTest('lastName', 'string');
    }

    public function testSetFirstName(): void
    {
        $this->basicSetTest('firstName', 'string');
    }

    public function testSetMiddleName(): void
    {
        $this->basicSetTest('middleName', 'string');
    }

    public function testSetDisplayName(): void
    {
        $this->basicSetTest('displayName', 'string');
    }

    public function testSetPhone(): void
    {
        $this->basicSetTest('phone', 'phone');
    }

    public function testSetEmail(): void
    {
        $this->basicSetTest('email', 'email');
    }

    public function testSetPreferredEmail(): void
    {
        $this->basicSetTest('preferredEmail', 'email');
    }

    public function testSetPronouns(): void
    {
        $this->basicSetTest('pronouns', 'string');
    }

    public function testSetAddedViaIlios(): void
    {
        $this->booleanSetTest('addedViaIlios');
    }

    public function testSetEnabled(): void
    {
        $this->booleanSetTest('enabled');
    }

    public function testSetCampusId(): void
    {
        $this->basicSetTest('campusId', 'string');
    }

    public function testSetOtherId(): void
    {
        $this->basicSetTest('otherId', 'string');
    }

    public function testSetExamined(): void
    {
        $this->booleanSetTest('examined');
    }

    public function testSetUserSyncIgnore(): void
    {
        $this->booleanSetTest('userSyncIgnore');
    }

    public function testSetIcsFeedKey(): void
    {
        $this->basicSetTest('icsFeedKey', 'string');
    }

    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    public function testAddAuditLog(): void
    {
        $this->entityCollectionAddTest('auditLog', 'AuditLog');
    }

    public function testRemoveAuditLog(): void
    {
        $this->entityCollectionRemoveTest('auditLog', 'AuditLog');
    }

    public function testSetAuditLogs(): void
    {
        $this->entityCollectionSetTest('auditLog', 'AuditLog');
    }

    public function testAddDirectedCourse(): void
    {
        $this->entityCollectionAddTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    public function testRemoveDirectedCourse(): void
    {
        $this->entityCollectionRemoveTest('directedCourse', 'Course', false, false, false, 'removeDirector');
    }

    public function testGetDirectedCourses(): void
    {
        $this->entityCollectionSetTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    public function testAddStudentAdvisedCourse(): void
    {
        $this->entityCollectionAddTest('studentAdvisedCourse', 'Course', false, false, 'addStudentAdvisor');
    }

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

    public function testGetStudentAdvisedCourses(): void
    {
        $this->entityCollectionSetTest('studentAdvisedCourse', 'Course', false, false, 'addStudentAdvisor');
    }

    public function testAddAdministeredSession(): void
    {
        $this->entityCollectionAddTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    public function testRemoveAdministeredSession(): void
    {
        $this->entityCollectionRemoveTest('administeredSession', 'Session', false, false, false, 'removeAdministrator');
    }

    public function testGetAdministeredSessions(): void
    {
        $this->entityCollectionSetTest('administeredSession', 'Session', false, false, 'addAdministrator');
    }

    public function testAddStudentAdvisedSession(): void
    {
        $this->entityCollectionAddTest('studentAdvisedSession', 'Session', false, false, 'addStudentAdvisor');
    }

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

    public function testGetStudentAdvisedSessions(): void
    {
        $this->entityCollectionSetTest('studentAdvisedSession', 'Session', false, false, 'addStudentAdvisor');
    }

    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup', false, false, false, 'removeUser');
    }

    public function testSetLearnerGroups(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    public function testAddInstructedLearnerGroup(): void
    {
        $this->entityCollectionAddTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

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

    public function testGetInstructedLearnerGroups(): void
    {
        $this->entityCollectionSetTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    public function testAddInstructorGroup(): void
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    public function testRemoveInstructorGroup(): void
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup', false, false, false, 'removeUser');
    }

    public function testSetInstructorGroups(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    public function testAddOffering(): void
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearner');
    }

    public function testRemoveOffering(): void
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearner');
    }

    public function testSetOfferings(): void
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearner');
    }

    public function testAddProgramYear(): void
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    public function testRemoveProgramYear(): void
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeDirector');
    }

    public function testSetProgramYears(): void
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    public function testAddAlert(): void
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addInstigator');
    }

    public function testRemoveAlert(): void
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeInstigator');
    }

    public function testSetAlerts(): void
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addInstigator');
    }

    public function testAddRole(): void
    {
        $this->entityCollectionAddTest('role', 'UserRole');
    }

    public function testRemoveRole(): void
    {
        $this->entityCollectionRemoveTest('role', 'UserRole');
    }

    public function testSetRoles(): void
    {
        $this->entityCollectionSetTest('role', 'UserRole');
    }

    public function testAddLearningMaterial(): void
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    public function testRemoveLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    public function testSetLearningMaterials(): void
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    public function testAddReport(): void
    {
        $this->entityCollectionAddTest('report', 'Report');
    }

    public function testRemoveReport(): void
    {
        $this->entityCollectionRemoveTest('report', 'Report');
    }

    public function testSetReports(): void
    {
        $this->entityCollectionSetTest('report', 'Report');
    }

    public function testAddCohort(): void
    {
        $this->entityCollectionAddTest('cohort', 'Cohort');
    }

    public function testRemoveCohort(): void
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort');
    }

    public function testSetCohorts(): void
    {
        $obj = m::mock(Cohort::class);
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $obj2 = m::mock(Cohort::class);
        $this->object->setCohorts(new ArrayCollection([$obj2]));
        $this->assertNull($this->object->getPrimaryCohort());
    }

    public function testAddInstructedOffering(): void
    {
        $this->entityCollectionAddTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    public function testRemoveInstructedOffering(): void
    {
        $this->entityCollectionRemoveTest('instructedOffering', 'Offering', false, false, false, 'removeInstructor');
    }

    public function testSetInstructedOffering(): void
    {
        $this->entityCollectionSetTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    public function testAddInstructorIlmSessions(): void
    {
        $this->entityCollectionAddTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

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

    public function testGetInstructorIlmSessions(): void
    {
        $this->entityCollectionSetTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    public function testAddLearnerIlmSessions(): void
    {
        $this->entityCollectionAddTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

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

    public function testGetLearnerIlmSessions(): void
    {
        $this->entityCollectionSetTest('learnerIlmSession', 'IlmSession', false, false, 'addLearner');
    }

    public function testSetPrimaryCohort(): void
    {
        $obj = m::mock(Cohort::class);
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $this->assertSame($obj, $this->object->getPrimaryCohort());
        $this->assertTrue($this->object->getCohorts()->contains($obj));
    }

    public function testAddPendingUserUpdates(): void
    {
        $this->entityCollectionAddTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    public function testRemovePendingUserUpdates(): void
    {
        $this->entityCollectionRemoveTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    public function testGetPendingUserUpdates(): void
    {
        $this->entityCollectionSetTest('pendingUserUpdate', 'PendingUserUpdate');
    }

    public function testAddDirectedSchool(): void
    {
        $this->entityCollectionAddTest('directedSchool', 'School', false, false, 'addDirector');
    }

    public function testRemoveDirectedSchool(): void
    {
        $this->entityCollectionRemoveTest('directedSchool', 'School', false, false, false, 'removeDirector');
    }

    public function testGetDirectedSchools(): void
    {
        $this->entityCollectionSetTest('directedSchool', 'School', false, false, 'addDirector');
    }

    public function testAddAdministeredCourse(): void
    {
        $this->entityCollectionAddTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    public function testRemoveAdministeredCourse(): void
    {
        $this->entityCollectionRemoveTest('administeredCourse', 'Course', false, false, false, 'removeAdministrator');
    }

    public function testGetAdministeredCourses(): void
    {
        $this->entityCollectionSetTest('administeredCourse', 'Course', false, false, 'addAdministrator');
    }

    public function testAddAdministeredSchool(): void
    {
        $this->entityCollectionAddTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    public function testRemoveAdministeredSchool(): void
    {
        $this->entityCollectionRemoveTest('administeredSchool', 'School', false, false, false, 'removeAdministrator');
    }

    public function testGetAdministeredSchools(): void
    {
        $this->entityCollectionSetTest('administeredSchool', 'School', false, false, 'addAdministrator');
    }

    public function testAddDirectedProgram(): void
    {
        $this->entityCollectionAddTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    public function testRemoveDirectedProgram(): void
    {
        $this->entityCollectionRemoveTest('directedProgram', 'Program', false, false, false, 'removeDirector');
    }

    public function testGetDirectedPrograms(): void
    {
        $this->entityCollectionSetTest('directedProgram', 'Program', false, false, 'addDirector');
    }

    public function testIsRoot(): void
    {
        $this->booleanSetTest('root');
    }

    public function testSetAuthentication(): void
    {
        $obj = m::mock(Authentication::class);
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
    }

    public function testSetAuthenticationNull(): void
    {
        $obj = m::mock(Authentication::class);
        $obj->shouldReceive('setUser')->with($this->object)->once();
        $this->object->setAuthentication($obj);
        $this->assertSame($obj, $this->object->getAuthentication());
        $this->object->setAuthentication(null);
        $this->assertSame(null, $this->object->getAuthentication());
    }

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

    public function testAddSessionMaterialStatus(): void
    {
        $this->entityCollectionAddTest(
            'sessionMaterialStatus',
            'UserSessionMaterialStatus',
            'getSessionMaterialStatuses',
            'addSessionMaterialStatus'
        );
    }

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
