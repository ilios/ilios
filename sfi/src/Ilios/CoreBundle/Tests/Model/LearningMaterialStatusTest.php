<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\LearningMaterialStatus;
use Mockery as m;

/**
 * Tests for Model LearningMaterialStatus
 */
class LearningMaterialStatusTest extends ModelBase
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

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialStatus::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearningMaterials());
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialStatus::getLearningMaterialStatusId
     */
    public function testGetLearningMaterialStatusId()
    {
        $this->basicGetTest('learningMaterialStatusId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialStatus::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialStatus::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialStatus::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->modelCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialStatus::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->modelCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialStatus::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        
        $this->modelCollectionGetTest('learningMaterial', 'LearningMaterial');
    }
}
