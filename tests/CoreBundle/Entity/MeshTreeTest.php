<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\MeshTree;
use Mockery as m;

/**
 * Tests for Entity MeshTree
 */
class MeshTreeTest extends EntityBase
{
    /**
     * @var MeshTree
     */
    protected $object;

    /**
     * Instantiate a MeshTree object
     */
    protected function setUp()
    {
        $this->object = new MeshTree;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'treeNumber'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTreeNumber('junk');
        $this->validate(0);
    }
    
    /**
     * @covers \Ilios\CoreBundle\Entity\MeshTree::setTreeNumber
     * @covers \Ilios\CoreBundle\Entity\MeshTree::getTreeNumber
     */
    public function testSetTreeNumber()
    {
        $this->basicSetTest('treeNumber', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshTree::getDescriptor
     * @covers \Ilios\CoreBundle\Entity\MeshTree::setDescriptor
     */
    public function testSetDescriptor()
    {
        $this->entitySetTest('descriptor', "MeshDescriptor");
    }
}
