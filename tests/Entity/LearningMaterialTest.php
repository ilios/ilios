<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
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
 */
#[Group('model')]
#[CoversClass(LearningMaterial::class)]
final class LearningMaterialTest extends EntityBase
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

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCourseLearningMaterials());
        $this->assertCount(0, $this->object->getSessionLearningMaterials());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testSetOriginalAuthor(): void
    {
        $this->basicSetTest('originalAuthor', 'string');
    }

    public function testGetOwningSchool(): void
    {
        $school = new School();
        $user = new User();
        $user->setSchool($school);
        $lm = new LearningMaterial();
        $lm->setOwningUser($user);
        $this->assertSame($school, $lm->getOwningSchool());
    }

    public function testAddCourseLearningMaterial(): void
    {
        $this->entityCollectionAddTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    public function testRemoveCourseLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    public function testGetCourseLearningMaterials(): void
    {
        $this->entityCollectionSetTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    public function testAddSessionLearningMaterial(): void
    {
        $this->entityCollectionAddTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    public function testRemoveSessionLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    public function testGetSessionLearningMaterials(): void
    {
        $this->entityCollectionSetTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

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
