<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\LearningMaterialUserRole;
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
     * @covers \AppBundle\Entity\LearningMaterialUserRole::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearningMaterials());
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterialUserRole::setTitle
     * @covers \AppBundle\Entity\LearningMaterialUserRole::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterialUserRole::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterialUserRole::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterialUserRole::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }
}
