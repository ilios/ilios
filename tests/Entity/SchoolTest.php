<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\CourseInterface;
use App\Entity\School;
use Mockery as m;

/**
 * Tests for Entity School
 */
#[Group('model')]
#[CoversClass(School::class)]
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

    public function testSetTemplatePrefix(): void
    {
        $this->basicSetTest('templatePrefix', 'string');
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetIliosAdministratorEmail(): void
    {
        $this->basicSetTest('iliosAdministratorEmail', 'string');
    }

    public function testSetChangeAlertRecipients(): void
    {
        $this->basicSetTest('changeAlertRecipients', 'string');
    }

    public function testSetCurriculumInventoryInstitution(): void
    {
        $this->entitySetTest('curriculumInventoryInstitution', 'CurriculumInventoryInstitution');
    }

    public function testAddAlert(): void
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addRecipient');
    }

    public function testRemoveAlert(): void
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeRecipient');
    }

    public function testGetAlerts(): void
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addRecipient');
    }

    public function testAddCompetency(): void
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    public function testGetCompetencies(): void
    {
        $this->entityCollectionSetTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'setCompetencies'
        );
    }

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

    public function testAddCourse(): void
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    public function testRemoveCourse(): void
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    public function testGetCourses(): void
    {
        $this->entityCollectionSetTest('course', 'Course');
    }

    public function testAddProgram(): void
    {
        $this->entityCollectionAddTest('program', 'Program');
    }

    public function testRemoveProgram(): void
    {
        $this->entityCollectionRemoveTest('program', 'Program');
    }

    public function testGetPrograms(): void
    {
        $this->entityCollectionSetTest('program', 'Program');
    }

    public function testAddVocabulary(): void
    {
        $this->entityCollectionAddTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    public function testRemoveVocabulary(): void
    {
        $this->entityCollectionRemoveTest('vocabulary', 'Vocabulary', 'getVocabularies');
    }

    public function testGetVocabularies(): void
    {
        $this->entityCollectionSetTest('vocabulary', 'Vocabulary', 'getVocabularies', 'setVocabularies');
    }

    public function testAddInstructorGroup(): void
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    public function testRemoveInstructorGroup(): void
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    public function testSetInstructorGroup(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    public function testAddSessionType(): void
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    public function testRemoveSessionType(): void
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    public function testSetSessionType(): void
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }

    public function testAddDirector(): void
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedSchool');
    }

    public function testRemoveDirector(): void
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedSchool');
    }

    public function testGetDirectors(): void
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedSchool');
    }

    public function testAddAdministrator(): void
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    public function testRemoveAdministrator(): void
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredSchool');
    }

    public function testSetAdministrators(): void
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredSchool');
    }

    public function testAddConfiguration(): void
    {
        $this->entityCollectionAddTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    public function testRemoveConfiguration(): void
    {
        $this->entityCollectionRemoveTest('configuration', 'SchoolConfig', 'getConfigurations');
    }

    public function testGetConfigurations(): void
    {
        $this->entityCollectionSetTest('configuration', 'SchoolConfig', 'getConfigurations', 'setConfigurations');
    }

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
