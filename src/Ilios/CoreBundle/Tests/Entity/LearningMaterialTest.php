<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\LearningMaterial;
use Mockery as m;

/**
 * Tests for Entity LearningMaterial
 */
class LearningMaterialTest extends EntityBase
{
    /**
     * @var LearningMaterial
     */
    protected $object;

    /**
     * Instantiate a LearningMaterial object
     */
    protected function setUp()
    {
        $this->object = new LearningMaterial;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title',
            'description',
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->object->setDescription('description');
        $this->validate(0);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourseLearningMaterials());
        $this->assertEmpty($this->object->getSessionLearningMaterials());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setTitle
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setDescription
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setOriginalAuthor
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getOriginalAuthor
     */
    public function testSetOriginalAuthor()
    {
        $this->basicSetTest('originalAuthor', 'string');
    }
}
