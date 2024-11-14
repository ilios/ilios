<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\LearningMaterialUserRole;

/**
 * Tests for Entity LearningMaterialUserRole
 * @group model
 */
class LearningMaterialUserRoleTest extends EntityBase
{
    protected LearningMaterialUserRole $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new LearningMaterialUserRole();
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

        $this->object->setTitle('test up to 60 char');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\LearningMaterialUserRole::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getLearningMaterials());
    }

    /**
     * @covers \App\Entity\LearningMaterialUserRole::setTitle
     * @covers \App\Entity\LearningMaterialUserRole::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterialUserRole::addLearningMaterial
     */
    public function testAddLearningMaterial(): void
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterialUserRole::removeLearningMaterial
     */
    public function testRemoveLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterialUserRole::getLearningMaterials
     */
    public function testGetLearningMaterials(): void
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    protected function getObject(): LearningMaterialUserRole
    {
        return $this->object;
    }
}
