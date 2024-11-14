<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CourseInterface;
use App\Entity\School;
use Mockery as m;

/**
 * Tests for Entity School
 * @group model
 */
class SchoolTest extends EntityBase
{
    protected School $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new School();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
            'iliosAdministratorEmail',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->object->setIliosAdministratorEmail('dartajax@winner.net');
        $this->object->setTemplatePrefix('');
        $this->validate(0);
        $this->object->setTemplatePrefix('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\School::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getAlerts());
        $this->assertCount(0, $this->object->getCourses());
        $this->assertCount(0, $this->object->getPrograms());
        $this->assertCount(0, $this->object->getInstructorGroups());
        $this->assertCount(0, $this->object->getCompetencies());
        $this->assertCount(0, $this->object->getSessionTypes());
        $this->assertCount(0, $this->object->getVocabularies());
        $this->assertCount(0, $this->object->getConfigurations());
    }

    /**
     * @covers \App\Entity\School::setTemplatePrefix
     * @covers \App\Entity\School::getTemplatePrefix
     */
    public function testSetTemplatePrefix(): void
    {
        $this->basicSetTest('templatePrefix', 'string');
    }

    /**
     * @covers \App\Entity\School::setTitle
     * @covers \App\Entity\School::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\School::setIliosAdministratorEmail
     * @covers \App\Entity\School::getIliosAdministratorEmail
     */
    public function testSetIliosAdministratorEmail(): void
    {
        $this->basicSetTest('iliosAdministratorEmail', 'string');
    }

    /**
     * @covers \App\Entity\School::setChangeAlertRecipients
     * @covers \App\Entity\School::getChangeAlertRecipients
     */
    public function testSetChangeAlertRecipients(): void
    {
        $this->basicSetTest('changeAlertRecipients', 'string');
    }

    /**
     * @covers \App\Entity\School::setCurriculumInventoryInstitution
     * @covers \App\Entity\School::getCurriculumInventoryInstitution
     */
    public function testSetCurriculumInventoryInstitution(): void
    {
        $this->entitySetTest('curriculumInventoryInstitution', 'CurriculumInventoryInstitution');
    }

    /**
     * @covers \App\Entity\School::addAlert
     */
    public function testAddAlert(): void
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers \App\Entity\School::removeAlert
     */
    public function testRemoveAlert(): void
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeRecipient');
    }

    /**
     * @covers \App\Entity\School::getAlerts
     */
    public function testGetAlerts(): void
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers \App\Entity\School::addCompetency
     */
    public function testAddCompetency(): void
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    /**
     * @covers \App\Entity\School::getCompetencies
     */
    public function testGetCompetencies(): void
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
    public function testRemoveCompetency(): void
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
    public function testAddCourse(): void
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\School::removeCourse
     */
    public function testRemoveCourse(): void
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\School::getCourses
     */
    public function testGetCourses(): void
    {
        $this->entityCollectionSetTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\School::addProgram
     */
    public function testAddProgram(): void
    {
        $this->entityCollectionAddTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\School::removeProgram
     */
    public function testRemoveProgram(): void
    {
        $this->entityCollectionRemoveTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\School::getPrograms
     */
    public function testGetPrograms(): void
    {
        $this->entityCollectionSetTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\School::addVocabulary
     */
    public function testAddVocabulary(): void
    {
        $this->entityCollectionAddTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    /**
     * @covers \App\Entity\School::removeVocabulary
     */
    public function testRemoveVocabulary(): void
    {
        $this->entityCollectionRemoveTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    /**
     * @covers \App\Entity\School::getVocabularies
     * @covers \App\Entity\School::setVocabularies
     */
    public function testGetVocabularies(): void
    {
        $this->entityCollectionSetTest('vocabulary', 'Vocabulary', 'getVocabularies', 'setVocabularies');
    }

    /**
     * @covers \App\Entity\School::addInstructorGroup
     */
    public function testAddInstructorGroup(): void
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\School::removeInstructorGroup
     */
    public function testRemoveInstructorGroup(): void
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\School::setInstructorGroups
     */
    public function testSetInstructorGroup(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\School::addSessionType
     */
    public function testAddSessionType(): void
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    /**
     * @covers \App\Entity\School::removeSessionType
     */
    public function testRemoveSessionType(): void
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    /**
     * @covers \App\Entity\School::setSessionTypes
     */
    public function testSetSessionType(): void
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }

    /**
     * @covers \App\Entity\School::addDirector
     */
    public function testAddDirector(): void
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedSchool');
    }

    /**
     * @covers \App\Entity\School::removeDirector
     */
    public function testRemoveDirector(): void
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedSchool');
    }

    /**
     * @covers \App\Entity\School::getDirectors
     */
    public function testGetDirectors(): void
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedSchool');
    }

    /**
     * @covers \App\Entity\School::addAdministrator
     */
    public function testAddAdministrator(): void
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    /**
     * @covers \App\Entity\School::removeAdministrator
     */
    public function testRemoveAdministrator(): void
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredSchool');
    }

    /**
     * @covers \App\Entity\School::getAdministrators
     * @covers \App\Entity\School::setAdministrators
     */
    public function testSetAdministrators(): void
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    /**
     * @covers \App\Entity\School::addConfiguration
     */
    public function testAddConfiguration(): void
    {
        $this->entityCollectionAddTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    /**
     * @covers \App\Entity\School::removeConfiguration
     */
    public function testRemoveConfiguration(): void
    {
        $this->entityCollectionRemoveTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    /**
     * @covers \App\Entity\School::getConfigurations
     * @covers \App\Entity\School::setConfigurations
     */
    public function testGetConfigurations(): void
    {
        $this->entityCollectionSetTest('configuration', 'SchoolConfig', 'getConfigurations', 'setConfigurations');
    }

    /**
     * @covers \App\Entity\School::getIndexableCourses
     */
    public function testGetIndexableCourses(): void
    {
        $course1 = m::mock(CourseInterface::class);
        $course2 = m::mock(CourseInterface::class);
        $this->object->addCourse($course1);
        $this->object->addCourse($course2);


        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals([$course1, $course2], $rhett);
    }

    protected function getObject(): School
    {
        return $this->object;
    }
}
