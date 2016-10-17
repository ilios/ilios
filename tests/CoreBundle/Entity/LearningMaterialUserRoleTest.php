<?php
namespace Tests\CoreBundle\Entity;

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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test up to 60 char');
        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterialUserRole::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearningMaterials());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterialUserRole::setTitle
     * @covers \Ilios\CoreBundle\Entity\LearningMaterialUserRole::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterialUserRole::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterialUserRole::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterialUserRole::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }
}
