<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\MeshPreviousIndexing;

/**
 * Tests for Entity MeshPreviousIndexing
 */
#[Group('model')]
#[CoversClass(MeshPreviousIndexing::class)]
class MeshPreviousIndexingTest extends EntityBase
{
    protected MeshPreviousIndexing $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new MeshPreviousIndexing();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'previousIndexing',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setPreviousIndexing('a big load of 
                stuff in here up to 65 K characters 
                too much for me to imagine');
        $this->validate(0);
    }
    public function testSetPreviousIndexing(): void
    {
        $this->basicSetTest('previousIndexing', 'string');
    }

    public function testSetDescriptor(): void
    {
        $this->entitySetTest('descriptor', "MeshDescriptor");
    }

    protected function getObject(): MeshPreviousIndexing
    {
        return $this->object;
    }
}
