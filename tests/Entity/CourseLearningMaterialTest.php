<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterial;
use App\Entity\LearningMaterialInterface;
use Mockery as m;

/**
 * Tests for Entity CourseLearningMaterial
 * @group model
 */
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

    /**
     * @covers \App\Entity\CourseLearningMaterial::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->assertFalse($this->object->hasPublicNotes());
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::setNotes
     * @covers \App\Entity\CourseLearningMaterial::getNotes
     */
    public function testSetNotes(): void
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::setRequired
     * @covers \App\Entity\CourseLearningMaterial::isRequired
     */
    public function testSetRequired(): void
    {
        $this->booleanSetTest('required');
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::setPublicNotes
     * @covers \App\Entity\CourseLearningMaterial::hasPublicNotes
     */
    public function testSetPublicNotes(): void
    {
        $this->booleanSetTest('publicNotes', false);
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::setCourse
     * @covers \App\Entity\CourseLearningMaterial::getCourse
     */
    public function testSetCourse(): void
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::setLearningMaterial
     * @covers \App\Entity\CourseLearningMaterial::getLearningMaterial
     */
    public function testSetLearningMaterial(): void
    {
        $this->entitySetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor(): void
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor(): void
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors(): void
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::setPosition
     * @covers \App\Entity\CourseLearningMaterial::getPosition
     */
    public function testSetPosition(): void
    {
        $this->basicSetTest('position', 'integer');
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::setStartDate
     * @covers \App\Entity\CourseLearningMaterial::getStartDate
     */
    public function testSetStartDate(): void
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \App\Entity\CourseLearningMaterial::setEndDate
     * @covers \App\Entity\CourseLearningMaterial::getEndDate
     */
    public function testSetEndDate(): void
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    protected function getObject(): CourseLearningMaterial
    {
        return $this->object;
    }
}
