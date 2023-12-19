<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\LearningMaterialStatus;
use Mockery as m;

/**
 * Tests for Entity LearningMaterialStatus
 * @group model
 */
class LearningMaterialStatusTest extends EntityBase
{
    protected function setUp(): void
    {
        $this->object = new LearningMaterialStatus();
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title'
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
        $this->assertEmpty($this->object->getLearningMaterials());
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
}
