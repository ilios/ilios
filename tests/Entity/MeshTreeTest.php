<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\MeshTree;
use Mockery as m;

/**
 * Tests for Entity MeshTree
 * @group model
 */
class MeshTreeTest extends EntityBase
{
    protected function setUp(): void
    {
        $this->object = new MeshTree();
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'treeNumber'
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTreeNumber('junk');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\MeshTree::setTreeNumber
     * @covers \App\Entity\MeshTree::getTreeNumber
     */
    public function testSetTreeNumber(): void
    {
        $this->basicSetTest('treeNumber', 'string');
    }

    /**
     * @covers \App\Entity\MeshTree::getDescriptor
     * @covers \App\Entity\MeshTree::setDescriptor
     */
    public function testSetDescriptor(): void
    {
        $this->entitySetTest('descriptor', "MeshDescriptor");
    }
}
