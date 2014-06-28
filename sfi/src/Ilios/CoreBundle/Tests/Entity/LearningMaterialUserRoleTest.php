<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\LearningMaterialUserRole;
use Mockery as m;

/**
 * Tests for Entity LearningMaterialUserRole
 */
class LearningMaterialUserRoleTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\LearningMaterialUserRole::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearningMaterials());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialUserRole::getLearningMaterialUserRoleId
     */
    public function testGetLearningMaterialUserRoleId()
    {
        $this->basicGetTest('learningMaterialUserRoleId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialUserRole::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialUserRole::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialUserRole::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialUserRole::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterialUserRole::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionGetTest('learningMaterial', 'LearningMaterial');
    }
}
