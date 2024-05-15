<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\MeshPreviousIndexing;

/**
 * Tests for Entity MeshPreviousIndexing
 * @group model
 */
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
    /**
     * @covers \App\Entity\MeshPreviousIndexing::setPreviousIndexing
     * @covers \App\Entity\MeshPreviousIndexing::getPreviousIndexing
     */
    public function testSetPreviousIndexing(): void
    {
        $this->basicSetTest('previousIndexing', 'string');
    }

    /**
     * @covers \App\Entity\MeshPreviousIndexing::getDescriptor
     * @covers \App\Entity\MeshPreviousIndexing::setDescriptor
     */
    public function testSetDescriptor(): void
    {
        $this->entitySetTest('descriptor', "MeshDescriptor");
    }

    protected function getObject(): MeshPreviousIndexing
    {
        return $this->object;
    }
}
