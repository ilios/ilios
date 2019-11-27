<?php
namespace App\Tests\Entity;

use App\Entity\LearningMaterialStatus;
use Mockery as m;

/**
 * Tests for Entity LearningMaterialStatus
 * @group model
 */
class LearningMaterialStatusTest extends EntityBase
{
    /**
     * @var LearningMaterialStatus
     */
    protected $object;

    /**
     * Instantiate a LearningMaterialStatus object
     */
    protected function setUp()
    {
        $this->object = new LearningMaterialStatus;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearningMaterials());
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::setTitle
     * @covers \App\Entity\LearningMaterialStatus::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterialStatus::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {

        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }
}
