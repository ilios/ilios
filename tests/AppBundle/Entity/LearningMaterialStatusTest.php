<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\LearningMaterialStatus;
use Mockery as m;

/**
 * Tests for Entity LearningMaterialStatus
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
     * @covers \AppBundle\Entity\LearningMaterialStatus::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearningMaterials());
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterialStatus::setTitle
     * @covers \AppBundle\Entity\LearningMaterialStatus::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterialStatus::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterialStatus::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterialStatus::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {

        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }
}
