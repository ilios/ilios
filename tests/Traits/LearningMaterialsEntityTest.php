<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\LearningMaterial;
use App\Traits\LearningMaterialsEntity;
use Mockery as m;
use App\Tests\TestCase;

#[CoversClass(LearningMaterialsEntity::class)]
class LearningMaterialsEntityTest extends TestCase
{
    private object $traitObject;
    public function setUp(): void
    {
        parent::setUp();
        $traitName = LearningMaterialsEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testSetLearningMaterials(): void
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(LearningMaterial::class));
        $collection->add(m::mock(LearningMaterial::class));
        $collection->add(m::mock(LearningMaterial::class));

        $this->traitObject->setLearningMaterials($collection);
        $this->assertEquals($collection, $this->traitObject->getLearningMaterials());
    }

    public function testRemoveLearningMaterial(): void
    {
        $collection = new ArrayCollection();
        $one = m::mock(LearningMaterial::class);
        $two = m::mock(LearningMaterial::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setLearningMaterials($collection);
        $this->traitObject->removeLearningMaterial($one);
        $learningMaterials = $this->traitObject->getLearningMaterials();
        $this->assertEquals(1, $learningMaterials->count());
        $this->assertEquals($two, $learningMaterials->first());
    }

    public function testAddLearningMaterial(): void
    {
        $this->traitObject->setLearningMaterials(new ArrayCollection());
        $this->assertCount(0, $this->traitObject->getLearningMaterials());

        $one = m::mock(LearningMaterial::class);
        $this->traitObject->addLearningMaterial($one);
        $this->assertCount(1, $this->traitObject->getLearningMaterials());
        $this->assertEquals($one, $this->traitObject->getLearningMaterials()->first());
        // duplicate prevention check
        $this->traitObject->addLearningMaterial($one);
        $this->assertCount(1, $this->traitObject->getLearningMaterials());
        $this->assertEquals($one, $this->traitObject->getLearningMaterials()->first());

        $two = m::mock(LearningMaterial::class);
        $this->traitObject->addLearningMaterial($two);
        $this->assertCount(2, $this->traitObject->getLearningMaterials());
        $this->assertEquals($one, $this->traitObject->getLearningMaterials()->first());
        $this->assertEquals($two, $this->traitObject->getLearningMaterials()->last());
    }
}
