<?php
namespace App\Tests\Entity;

use App\Entity\LearningMaterialUserRole;
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
     * @covers \App\Entity\LearningMaterialUserRole::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearningMaterials());
    }

    /**
     * @covers \App\Entity\LearningMaterialUserRole::setTitle
     * @covers \App\Entity\LearningMaterialUserRole::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterialUserRole::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterialUserRole::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterialUserRole::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }
}
