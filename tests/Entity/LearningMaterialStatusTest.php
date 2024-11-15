<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\LearningMaterialStatus;

/**
 * Tests for Entity LearningMaterialStatus
 */
#[Group('model')]
#[CoversClass(LearningMaterialStatus::class)]
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

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getLearningMaterials());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testAddLearningMaterial(): void
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    public function testRemoveLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    public function testGetLearningMaterials(): void
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    protected function getObject(): LearningMaterialStatus
    {
        return $this->object;
    }
}
