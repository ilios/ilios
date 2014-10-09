<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\LearningMaterialUserRole;
use Mockery as m;

/**
 * Tests for Model LearningMaterialUserRole
 */
class LearningMaterialUserRoleTest extends ModelBase
{
    /**
     * @var LearningMaterialUserRole
     */
    protected $object;

    /**
     * Instantiate a LearningMaterialUserRole object
     */
    protected function setUp()
    {
        $this->object = new LearningMaterialUserRole;
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialUserRole::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearningMaterials());
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialUserRole::getLearningMaterialUserRoleId
     */
    public function testGetLearningMaterialUserRoleId()
    {
        $this->basicGetTest('learningMaterialUserRoleId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialUserRole::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialUserRole::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialUserRole::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->modelCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialUserRole::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->modelCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterialUserRole::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->modelCollectionGetTest('learningMaterial', 'LearningMaterial');
    }
}
