<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\LearningMaterialStatus;
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

    // No NotBlank() fields so no validation required on that 
    //

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialStatus::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearningMaterials());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialStatus::setTitle
     * @covers Ilios\CoreBundle\Entity\LearningMaterialStatus::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialStatus::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialStatus::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {

        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }
}
