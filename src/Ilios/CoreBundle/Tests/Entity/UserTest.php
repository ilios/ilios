<?php

namespace Ilios\CoreBundle\Tests\Entity;

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
        $this->assertEmpty($this->object->getCohorts());
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
        $this->softDeleteEntityCollectionAddTest('directedCourse', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getDirectedCourses
     */
    public function testGetDirectedCourses()
    {
        $this->softDeleteEntityCollectionSetTest('directedCourse', 'Course');
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
        $this->softDeleteEntityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getOfferings
     */
    public function testSetOfferings()
    {
        $this->softDeleteEntityCollectionSetTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->softDeleteEntityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getProgramYears
     */
    public function testSetProgramYears()
    {
        $this->softDeleteEntityCollectionSetTest('programYear', 'ProgramYear');
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
        $this->softDeleteEntityCollectionAddTest('report', 'Report');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getReports
     */
    public function testSetReports()
    {
        $this->softDeleteEntityCollectionSetTest('report', 'Report');
    }


    /**
     * @covers Ilios\CoreBundle\Entity\User::addCohort
     */
    public function testAddCohort()
    {
        $this->entityCollectionAddTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getCohorts
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
     * @covers Ilios\CoreBundle\Entity\User::addInstructedOffering
     */
    public function testAddInstructedOffering()
    {
        $this->entityCollectionAddTest('instructedOffering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getInstructedOfferings
     */
    public function testSetInstructedOffering()
    {
        $this->entityCollectionSetTest('instructedOffering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setPrimaryCohort
     * @covers Ilios\CoreBundle\Entity\User::getPrimaryCohort
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
}
