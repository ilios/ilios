<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\LearningMaterialInterface;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterial;
use Mockery as m;

/**
 * Tests for Entity SessionLearningMaterial
 */
#[Group('model')]
#[CoversClass(SessionLearningMaterial::class)]
class SessionLearningMaterialTest extends EntityBase
{
    protected SessionLearningMaterial $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new SessionLearningMaterial();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getMeshDescriptors());
    }

    public function testNotBlankValidation(): void
    {
        $notNull = [
            'required',
            'session',
            'learningMaterial',
        ];
        $this->validateNotNulls($notNull);

        $this->object->setRequired(false);
        $this->object->setSession(m::mock(SessionInterface::class));
        $this->object->setLearningMaterial(m::mock(LearningMaterialInterface::class));
        $this->object->setNotes('');
        $this->validate(0);
        $this->object->setNotes('test');
        $this->validate(0);
    }

    public function testSetNotes(): void
    {
        $this->basicSetTest('notes', 'string');
    }

    public function testSetRequired(): void
    {
        $this->booleanSetTest('required');
    }

    public function testSetNotesArePublic(): void
    {
        $this->booleanSetTest('publicNotes', false);
    }

    public function testSetSession(): void
    {
        $this->entitySetTest('session', 'Session');
    }

    public function testSetLearningMaterial(): void
    {
        $this->entitySetTest('learningMaterial', "LearningMaterial");
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

    protected function getObject(): SessionLearningMaterial
    {
        return $this->object;
    }
}
