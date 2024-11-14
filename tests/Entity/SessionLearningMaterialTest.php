<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\LearningMaterialInterface;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterial;
use Mockery as m;

/**
 * Tests for Entity SessionLearningMaterial
 * @group model
 */
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

    /**
     * @covers \App\Entity\SessionLearningMaterial::__construct
     */
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

    /**
     * @covers \App\Entity\SessionLearningMaterial::setNotes
     * @covers \App\Entity\SessionLearningMaterial::getNotes
     */
    public function testSetNotes(): void
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::setRequired
     * @covers \App\Entity\SessionLearningMaterial::isRequired
     */
    public function testSetRequired(): void
    {
        $this->booleanSetTest('required');
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::setPublicNotes
     * @covers \App\Entity\SessionLearningMaterial::hasPublicNotes
     */
    public function testSetNotesArePublic(): void
    {
        $this->booleanSetTest('publicNotes', false);
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::setSession
     * @covers \App\Entity\SessionLearningMaterial::getSession
     */
    public function testSetSession(): void
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::setLearningMaterial
     * @covers \App\Entity\SessionLearningMaterial::getLearningMaterial
     */
    public function testSetLearningMaterial(): void
    {
        $this->entitySetTest('learningMaterial', "LearningMaterial");
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor(): void
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor(): void
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors(): void
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::setPosition
     * @covers \App\Entity\SessionLearningMaterial::getPosition
     */
    public function testSetPosition(): void
    {
        $this->basicSetTest('position', 'integer');
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::setStartDate
     * @covers \App\Entity\SessionLearningMaterial::getStartDate
     */
    public function testSetStartDate(): void
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \App\Entity\SessionLearningMaterial::setEndDate
     * @covers \App\Entity\SessionLearningMaterial::getEndDate
     */
    public function testSetEndDate(): void
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    protected function getObject(): SessionLearningMaterial
    {
        return $this->object;
    }
}
