<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\LearningMaterial;
use App\Entity\LearningMaterialStatusInterface;
use App\Entity\LearningMaterialUserRoleInterface;
use App\Entity\School;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\User;
use App\Entity\UserInterface;
use Mockery as m;

/**
 * Tests for Entity LearningMaterial
 * @group model
 */
class LearningMaterialTest extends EntityBase
{
    protected LearningMaterial $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new LearningMaterial();
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
        ];
        $this->object->setUserRole(m::mock(LearningMaterialUserRoleInterface::class));
        $this->object->setStatus(m::mock(LearningMaterialStatusInterface::class));
        $this->object->setOwningUser(m::mock(UserInterface::class));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->object->setDescription('');
        $this->object->setOriginalAuthor('');
        $this->validate(0);
        $this->object->setDescription('test');
        $this->object->setOriginalAuthor('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNulls = [
            'userRole',
            'status',
            'owningUser',
        ];
        $this->object->setTitle('test');

        $this->validateNotNulls($notNulls);

        $this->object->setUserRole(m::mock(LearningMaterialUserRoleInterface::class));
        $this->object->setStatus(m::mock(LearningMaterialStatusInterface::class));
        $this->object->setOwningUser(m::mock(UserInterface::class));

        $this->validate(0);
    }

    /**
     * @covers \App\Entity\LearningMaterial::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCourseLearningMaterials());
        $this->assertCount(0, $this->object->getSessionLearningMaterials());
    }

    /**
     * @covers \App\Entity\LearningMaterial::setTitle
     * @covers \App\Entity\LearningMaterial::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterial::setDescription
     * @covers \App\Entity\LearningMaterial::getDescription
     */
    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterial::setOriginalAuthor
     * @covers \App\Entity\LearningMaterial::getOriginalAuthor
     */
    public function testSetOriginalAuthor(): void
    {
        $this->basicSetTest('originalAuthor', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getOwningSchool
     */
    public function testGetOwningSchool(): void
    {
        $school = new School();
        $user = new User();
        $user->setSchool($school);
        $lm = new LearningMaterial();
        $lm->setOwningUser($user);
        $this->assertSame($school, $lm->getOwningSchool());
    }

    /**
     * @covers \App\Entity\LearningMaterial::addCourseLearningMaterial
     */
    public function testAddCourseLearningMaterial(): void
    {
        $this->entityCollectionAddTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::removeCourseLearningMaterial
     */
    public function testRemoveCourseLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getCourseLearningMaterials
     * @covers \App\Entity\LearningMaterial::setCourseLearningMaterials
     */
    public function testGetCourseLearningMaterials(): void
    {
        $this->entityCollectionSetTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::addSessionLearningMaterial
     */
    public function testAddSessionLearningMaterial(): void
    {
        $this->entityCollectionAddTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::removeSessionLearningMaterial
     */
    public function testRemoveSessionLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getSessionLearningMaterials
     * @covers \App\Entity\LearningMaterial::setSessionLearningMaterials
     */
    public function testGetSessionLearningMaterials(): void
    {
        $this->entityCollectionSetTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getIndexableCourses
     */
    public function testGetIndexableCourses(): void
    {
        $course1 = m::mock(CourseInterface::class);
        $courseLearningMaterial = m::mock(CourseLearningMaterialInterface::class);
        $courseLearningMaterial->shouldReceive('getCourse')->once()->andReturn($course1);
        $this->object->addCourseLearningMaterial($courseLearningMaterial);

        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getCourse')->once()->andReturn($course2);
        $sessionLearningMaterial = m::mock(SessionLearningMaterialInterface::class);
        $sessionLearningMaterial->shouldReceive('getSession')->once()->andReturn($session);
        $this->object->addSessionLearningMaterial($sessionLearningMaterial);

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals([$course1, $course2], $rhett);
    }

    protected function getObject(): LearningMaterial
    {
        return $this->object;
    }
}
