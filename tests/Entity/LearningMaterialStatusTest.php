<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\LearningMaterialStatus;

/**
 * Tests for Entity LearningMaterialStatus
 * @group model
 */
class LearningMaterialStatusTest extends EntityBase
{
    protected LearningMaterialStatus $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new LearningMaterialStatus();
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
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getLearningMaterials());
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::setTitle
     * @covers \App\Entity\LearningMaterialStatus::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::addLearningMaterial
     */
    public function testAddLearningMaterial(): void
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::removeLearningMaterial
     */
    public function testRemoveLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::getLearningMaterials
     */
    public function testGetLearningMaterials(): void
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    protected function getObject(): LearningMaterialStatus
    {
        return $this->object;
    }
}
