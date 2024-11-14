<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryExport;

/**
 * Tests for Entity CurriculumInventoryExport
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\CurriculumInventoryExport::class)]
class CurriculumInventoryExportTest extends EntityBase
{
    protected CurriculumInventoryExport $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new CurriculumInventoryExport();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'document',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setDocument('text file super large test');
        $this->validate(0);
    }

    public function testSetDocument(): void
    {
        $this->basicSetTest('document', 'string');
    }

    public function testSetReport(): void
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    public function testSetCreatedBy(): void
    {
        $this->entitySetTest('createdBy', 'User');
    }

    public function testSetCreatedAt(): void
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    protected function getObject(): CurriculumInventoryExport
    {
        return $this->object;
    }
}
