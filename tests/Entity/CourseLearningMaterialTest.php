<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterial;
use App\Entity\LearningMaterialInterface;
use Mockery as m;

/**
 * Tests for Entity CourseLearningMaterial
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\CourseLearningMaterial::class)]
class CourseLearningMaterialTest extends EntityBase
{
    protected CourseLearningMaterial $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new CourseLearningMaterial();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notNull = [
            'course',
            'learningMaterial',
        ];
        $this->validateNotNulls($notNull);

        $this->object->setCourse(m::mock(CourseInterface::class));
        $this->object->setLearningMaterial(m::mock(LearningMaterialInterface::class));
        $this->validate(0);
        $this->object->setNotes('');
        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->assertFalse($this->object->hasPublicNotes());
    }

    public function testSetNotes(): void
    {
        $this->basicSetTest('notes', 'string');
    }

    public function testSetRequired(): void
    {
        $this->booleanSetTest('required');
    }

    public function testSetPublicNotes(): void
    {
        $this->booleanSetTest('publicNotes', false);
    }

    public function testSetCourse(): void
    {
        $this->entitySetTest('course', 'Course');
    }

    public function testSetLearningMaterial(): void
    {
        $this->entitySetTest('learningMaterial', 'LearningMaterial');
    }

    public function testAddMeshDescriptor(): void
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    public function testRemoveMeshDescriptor(): void
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    public function testGetMeshDescriptors(): void
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    public function testSetPosition(): void
    {
        $this->basicSetTest('position', 'integer');
    }

    public function testSetStartDate(): void
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    public function testSetEndDate(): void
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    protected function getObject(): CourseLearningMaterial
    {
        return $this->object;
    }
}
