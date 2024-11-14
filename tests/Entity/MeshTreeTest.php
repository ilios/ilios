<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\MeshTree;

/**
 * Tests for Entity MeshTree
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\MeshTree::class)]
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
