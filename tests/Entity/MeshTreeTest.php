<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\MeshTree;

/**
 * Tests for Entity MeshTree
 */
#[Group('model')]
#[CoversClass(MeshTree::class)]
class MeshTreeTest extends EntityBase
{
    protected MeshTree $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new MeshTree();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'treeNumber',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTreeNumber('junk');
        $this->validate(0);
    }

    public function testSetTreeNumber(): void
    {
        $this->basicSetTest('treeNumber', 'string');
    }

    public function testSetDescriptor(): void
    {
        $this->entitySetTest('descriptor', "MeshDescriptor");
    }

    protected function getObject(): MeshTree
    {
        return $this->object;
    }
}
