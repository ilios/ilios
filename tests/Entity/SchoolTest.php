<?php
namespace Tests\App\Entity;

use App\Entity\School;
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
     * @covers \App\Entity\School::__construct
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
     * @covers \App\Entity\School::setTemplatePrefix
     * @covers \App\Entity\School::getTemplatePrefix
     */
    public function testSetTemplatePrefix()
    {
        $this->basicSetTest('templatePrefix', 'string');
    }

    /**
     * @covers \App\Entity\School::setTitle
     * @covers \App\Entity\School::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\School::setIliosAdministratorEmail
     * @covers \App\Entity\School::getIliosAdministratorEmail
     */
    public function testSetIliosAdministratorEmail()
    {
        $this->basicSetTest('iliosAdministratorEmail', 'string');
    }

    /**
     * @covers \App\Entity\School::setChangeAlertRecipients
     * @covers \App\Entity\School::getChangeAlertRecipients
     */
    public function testSetChangeAlertRecipients()
    {
        $this->entitySetTest('changeAlertRecipients', 'ChangeAlertRecipients');
    }

    /**
     * @covers \App\Entity\School::setCurriculumInventoryInstitution
     * @covers \App\Entity\School::getCurriculumInventoryInstitution
     */
    public function testSetCurriculumInventoryInstitution()
    {
        $this->entitySetTest('curriculumInventoryInstitution', 'CurriculumInventoryInstitution');
    }

    /**
     * @covers \App\Entity\School::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers \App\Entity\School::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeRecipient');
    }

    /**
     * @covers \App\Entity\School::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers \App\Entity\School::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    /**
     * @covers \App\Entity\School::getCompetencies
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
     * @covers \App\Entity\School::removeCompetency
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
     * @covers \App\Entity\School::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\School::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\School::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\School::addProgram
     */
    public function testAddProgram()
    {
        $this->entityCollectionAddTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\School::removeProgram
     */
    public function testRemoveProgram()
    {
        $this->entityCollectionRemoveTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\School::getPrograms
     */
    public function testGetPrograms()
    {
        $this->entityCollectionSetTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\School::addDepartment
     */
    public function testAddDepartment()
    {
        $this->entityCollectionAddTest('department', 'Department');
    }

    /**
     * @covers \App\Entity\School::removeDepartment
     */
    public function testRemoveDepartment()
    {
        $this->entityCollectionRemoveTest('department', 'Department');
    }

    /**
     * @covers \App\Entity\School::getDepartments
     * @covers \App\Entity\School::setDepartments
     */
    public function testGetDepartments()
    {
        $this->entityCollectionSetTest('department', 'Department');
    }

    /**
     * @covers \App\Entity\School::addSteward
     */
    public function testAddSteward()
    {
        $this->entityCollectionAddTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \App\Entity\School::removeSteward
     */
    public function testRemoveSteward()
    {
        $this->entityCollectionRemoveTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \App\Entity\School::getStewards
     */
    public function testGetSteward()
    {
        $this->entityCollectionSetTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \App\Entity\School::addVocabulary
     */
    public function testAddVocabulary()
    {
        $this->entityCollectionAddTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    /**
     * @covers \App\Entity\School::removeVocabulary
     */
    public function testRemoveVocabulary()
    {
        $this->entityCollectionRemoveTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    /**
     * @covers \App\Entity\School::getVocabularies
     * @covers \App\Entity\School::setVocabularies
     */
    public function testGetVocabularies()
    {
        $this->entityCollectionSetTest('vocabulary', 'Vocabulary', 'getVocabularies', 'setVocabularies');
    }

    /**
     * @covers \App\Entity\School::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\School::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\School::setInstructorGroups
     */
    public function testSetInstructorGroup()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\School::addSessionType
     */
    public function testAddSessionType()
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    /**
     * @covers \App\Entity\School::removeSessionType
     */
    public function testRemoveSessionType()
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    /**
     * @covers \App\Entity\School::setSessionTypes
     */
    public function testSetSessionType()
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }

    /**
     * @covers \App\Entity\School::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedSchool');
    }

    /**
     * @covers \App\Entity\School::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedSchool');
    }

    /**
     * @covers \App\Entity\School::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedSchool');
    }

    /**
     * @covers \App\Entity\School::addAdministrator
     */
    public function testAddAdministrator()
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    /**
     * @covers \App\Entity\School::removeAdministrator
     */
    public function testRemoveAdministrator()
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredSchool');
    }

    /**
     * @covers \App\Entity\School::getAdministrators
     * @covers \App\Entity\School::setAdministrators
     */
    public function testSetAdministrators()
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    /**
     * @covers \App\Entity\School::addConfiguration
     */
    public function testAddConfiguration()
    {
        $this->entityCollectionAddTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    /**
     * @covers \App\Entity\School::removeConfiguration
     */
    public function testRemoveConfiguration()
    {
        $this->entityCollectionRemoveTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    /**
     * @covers \App\Entity\School::getConfigurations
     * @covers \App\Entity\School::setConfigurations
     */
    public function testGetConfigurations()
    {
        $this->entityCollectionSetTest('configuration', 'SchoolConfig', 'getConfigurations', 'setConfigurations');
    }
}
