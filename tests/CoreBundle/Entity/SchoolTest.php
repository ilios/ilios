<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\School;
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
     * @covers \Ilios\CoreBundle\Entity\School::__construct
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
     * @covers \Ilios\CoreBundle\Entity\School::setTemplatePrefix
     * @covers \Ilios\CoreBundle\Entity\School::getTemplatePrefix
     */
    public function testSetTemplatePrefix()
    {
        $this->basicSetTest('templatePrefix', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::setTitle
     * @covers \Ilios\CoreBundle\Entity\School::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::setIliosAdministratorEmail
     * @covers \Ilios\CoreBundle\Entity\School::getIliosAdministratorEmail
     */
    public function testSetIliosAdministratorEmail()
    {
        $this->basicSetTest('iliosAdministratorEmail', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::setChangeAlertRecipients
     * @covers \Ilios\CoreBundle\Entity\School::getChangeAlertRecipients
     */
    public function testSetChangeAlertRecipients()
    {
        $this->entitySetTest('changeAlertRecipients', 'ChangeAlertRecipients');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::setCurriculumInventoryInstitution
     * @covers \Ilios\CoreBundle\Entity\School::getCurriculumInventoryInstitution
     */
    public function testSetCurriculumInventoryInstitution()
    {
        $this->entitySetTest('curriculumInventoryInstitution', 'CurriculumInventoryInstitution');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeRecipient');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getCompetencies
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
     * @covers \Ilios\CoreBundle\Entity\School::removeCompetency
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
     * @covers \Ilios\CoreBundle\Entity\School::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addProgram
     */
    public function testAddProgram()
    {
        $this->entityCollectionAddTest('program', 'Program');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeProgram
     */
    public function testRemoveProgram()
    {
        $this->entityCollectionRemoveTest('program', 'Program');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getPrograms
     */
    public function testGetPrograms()
    {
        $this->entityCollectionSetTest('program', 'Program');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addDepartment
     */
    public function testAddDepartment()
    {
        $this->entityCollectionAddTest('department', 'Department');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeDepartment
     */
    public function testRemoveDepartment()
    {
        $this->entityCollectionRemoveTest('department', 'Department');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getDepartments
     * @covers \Ilios\CoreBundle\Entity\School::setDepartments
     */
    public function testGetDepartments()
    {
        $this->entityCollectionSetTest('department', 'Department');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addSteward
     */
    public function testAddSteward()
    {
        $this->entityCollectionAddTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeSteward
     */
    public function testRemoveSteward()
    {
        $this->entityCollectionRemoveTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getStewards
     */
    public function testGetSteward()
    {
        $this->entityCollectionSetTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addVocabulary
     */
    public function testAddVocabulary()
    {
        $this->entityCollectionAddTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeVocabulary
     */
    public function testRemoveVocabulary()
    {
        $this->entityCollectionRemoveTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getVocabularies
     * @covers \Ilios\CoreBundle\Entity\School::setVocabularies
     */
    public function testGetVocabularies()
    {
        $this->entityCollectionSetTest('vocabulary', 'Vocabulary', 'getVocabularies', 'setVocabularies');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::setInstructorGroups
     */
    public function testSetInstructorGroup()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addSessionType
     */
    public function testAddSessionType()
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeSessionType
     */
    public function testRemoveSessionType()
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::setSessionTypes
     */
    public function testSetSessionType()
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedSchool');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedSchool');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedSchool');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addAdministrator
     */
    public function testAddAdministrator()
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeAdministrator
     */
    public function testRemoveAdministrator()
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredSchool');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getAdministrators
     * @covers \Ilios\CoreBundle\Entity\School::setAdministrators
     */
    public function testSetAdministrators()
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addConfiguration
     */
    public function testAddConfiguration()
    {
        $this->entityCollectionAddTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeConfiguration
     */
    public function testRemoveConfiguration()
    {
        $this->entityCollectionRemoveTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getConfigurations
     * @covers \Ilios\CoreBundle\Entity\School::setConfigurations
     */
    public function testGetConfigurations()
    {
        $this->entityCollectionSetTest('configuration', 'SchoolConfig', 'getConfigurations', 'setConfigurations');
    }
}
