<?php

namespace Tests\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\PermissionMatrix;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class PermissionCheckerTest extends TestCase
{
    protected $permissionChecker;

    protected $permissionMatrix;


    public function setUp()
    {
        $this->permissionMatrix = new PermissionMatrix();
        $this->permissionChecker = new PermissionChecker($this->permissionMatrix);
    }

    public function tearDown()
    {
        unset($this->permissionChecker);
        unset($this->permissionMatrix);
    }

    public function testCanUpdateCourse()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteCourse()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCourse()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUnlockCourse()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUnarchiveCourse()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateSession()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteSession()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateSession()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateSessionType()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteSessionType()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateSessionType()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateDepartment()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteDepartment()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateDepartment()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateProgram()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteProgram()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateProgram()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUnlockProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUnarchiveProgramYear()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateCohort()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteCohort()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCohort()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateSchoolConfig()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteSchoolConfig()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateSchoolConfig()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateSchool()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteSchool()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateCompetency()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteCompetency()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCompetency()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateVocabulary()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteVocabulary()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateVocabulary()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateTerm()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteTerm()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateTerm()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateInstructorGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteInstructorGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateInstructorGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateCurriculumInventoryReport()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteCurriculumInventoryReport()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCurriculumInventoryReport()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateCurriculumInventoryInstitution()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteCurriculumInventoryInstitution()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateCurriculumInventoryInstitution()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateLearnerGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteLearnerGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateLearnerGroup()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanUpdateUser()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanDeleteUser()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCanCreateUser()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
