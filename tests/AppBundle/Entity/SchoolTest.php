<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\School;
use Mockery as m;

/**
 * Tests for Entity School
 */
class SchoolTest extends EntityBase
{
    /**
     * @var School
     */
    protected $object;

    /**
     * Instantiate a School object
     */
    protected function setUp()
    {
        $this->object = new School;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title',
            'iliosAdministratorEmail'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->object->setIliosAdministratorEmail('dartajax@winner.net');
        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\School::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
        $this->assertEmpty($this->object->getStewards());
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getPrograms());
        $this->assertEmpty($this->object->getDepartments());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getCompetencies());
        $this->assertEmpty($this->object->getSessionTypes());
        $this->assertEmpty($this->object->getVocabularies());
        $this->assertEmpty($this->object->getConfigurations());
    }

    /**
     * @covers \AppBundle\Entity\School::setTemplatePrefix
     * @covers \AppBundle\Entity\School::getTemplatePrefix
     */
    public function testSetTemplatePrefix()
    {
        $this->basicSetTest('templatePrefix', 'string');
    }

    /**
     * @covers \AppBundle\Entity\School::setTitle
     * @covers \AppBundle\Entity\School::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\School::setIliosAdministratorEmail
     * @covers \AppBundle\Entity\School::getIliosAdministratorEmail
     */
    public function testSetIliosAdministratorEmail()
    {
        $this->basicSetTest('iliosAdministratorEmail', 'string');
    }

    /**
     * @covers \AppBundle\Entity\School::setChangeAlertRecipients
     * @covers \AppBundle\Entity\School::getChangeAlertRecipients
     */
    public function testSetChangeAlertRecipients()
    {
        $this->entitySetTest('changeAlertRecipients', 'ChangeAlertRecipients');
    }

    /**
     * @covers \AppBundle\Entity\School::setCurriculumInventoryInstitution
     * @covers \AppBundle\Entity\School::getCurriculumInventoryInstitution
     */
    public function testSetCurriculumInventoryInstitution()
    {
        $this->entitySetTest('curriculumInventoryInstitution', 'CurriculumInventoryInstitution');
    }

    /**
     * @covers \AppBundle\Entity\School::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers \AppBundle\Entity\School::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeRecipient');
    }

    /**
     * @covers \AppBundle\Entity\School::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers \AppBundle\Entity\School::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    /**
     * @covers \AppBundle\Entity\School::getCompetencies
     */
    public function testGetCompetencies()
    {
        $this->entityCollectionSetTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'setCompetencies'
        );
    }

    /**
     * @covers \AppBundle\Entity\School::removeCompetency
     */
    public function testRemoveCompetency()
    {
        $this->entityCollectionRemoveTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'addCompetency',
            'removeCompetency'
        );
    }

    /**
     * @covers \AppBundle\Entity\School::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers \AppBundle\Entity\School::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers \AppBundle\Entity\School::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course');
    }

    /**
     * @covers \AppBundle\Entity\School::addProgram
     */
    public function testAddProgram()
    {
        $this->entityCollectionAddTest('program', 'Program');
    }

    /**
     * @covers \AppBundle\Entity\School::removeProgram
     */
    public function testRemoveProgram()
    {
        $this->entityCollectionRemoveTest('program', 'Program');
    }

    /**
     * @covers \AppBundle\Entity\School::getPrograms
     */
    public function testGetPrograms()
    {
        $this->entityCollectionSetTest('program', 'Program');
    }

    /**
     * @covers \AppBundle\Entity\School::addDepartment
     */
    public function testAddDepartment()
    {
        $this->entityCollectionAddTest('department', 'Department');
    }

    /**
     * @covers \AppBundle\Entity\School::removeDepartment
     */
    public function testRemoveDepartment()
    {
        $this->entityCollectionRemoveTest('department', 'Department');
    }

    /**
     * @covers \AppBundle\Entity\School::getDepartments
     * @covers \AppBundle\Entity\School::setDepartments
     */
    public function testGetDepartments()
    {
        $this->entityCollectionSetTest('department', 'Department');
    }

    /**
     * @covers \AppBundle\Entity\School::addSteward
     */
    public function testAddSteward()
    {
        $this->entityCollectionAddTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \AppBundle\Entity\School::removeSteward
     */
    public function testRemoveSteward()
    {
        $this->entityCollectionRemoveTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \AppBundle\Entity\School::getStewards
     */
    public function testGetSteward()
    {
        $this->entityCollectionSetTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \AppBundle\Entity\School::addVocabulary
     */
    public function testAddVocabulary()
    {
        $this->entityCollectionAddTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    /**
     * @covers \AppBundle\Entity\School::removeVocabulary
     */
    public function testRemoveVocabulary()
    {
        $this->entityCollectionRemoveTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    /**
     * @covers \AppBundle\Entity\School::getVocabularies
     * @covers \AppBundle\Entity\School::setVocabularies
     */
    public function testGetVocabularies()
    {
        $this->entityCollectionSetTest('vocabulary', 'Vocabulary', 'getVocabularies', 'setVocabularies');
    }

    /**
     * @covers \AppBundle\Entity\School::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\School::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\School::setInstructorGroups
     */
    public function testSetInstructorGroup()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\School::addSessionType
     */
    public function testAddSessionType()
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    /**
     * @covers \AppBundle\Entity\School::removeSessionType
     */
    public function testRemoveSessionType()
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    /**
     * @covers \AppBundle\Entity\School::setSessionTypes
     */
    public function testSetSessionType()
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }

    /**
     * @covers \AppBundle\Entity\School::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedSchool');
    }

    /**
     * @covers \AppBundle\Entity\School::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedSchool');
    }

    /**
     * @covers \AppBundle\Entity\School::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedSchool');
    }

    /**
     * @covers \AppBundle\Entity\School::addAdministrator
     */
    public function testAddAdministrator()
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    /**
     * @covers \AppBundle\Entity\School::removeAdministrator
     */
    public function testRemoveAdministrator()
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredSchool');
    }

    /**
     * @covers \AppBundle\Entity\School::getAdministrators
     * @covers \AppBundle\Entity\School::setAdministrators
     */
    public function testSetAdministrators()
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    /**
     * @covers \AppBundle\Entity\School::addConfiguration
     */
    public function testAddConfiguration()
    {
        $this->entityCollectionAddTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    /**
     * @covers \AppBundle\Entity\School::removeConfiguration
     */
    public function testRemoveConfiguration()
    {
        $this->entityCollectionRemoveTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    /**
     * @covers \AppBundle\Entity\School::getConfigurations
     * @covers \AppBundle\Entity\School::setConfigurations
     */
    public function testGetConfigurations()
    {
        $this->entityCollectionSetTest('configuration', 'SchoolConfig', 'getConfigurations', 'setConfigurations');
    }
}
