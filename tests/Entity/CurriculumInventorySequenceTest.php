<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequence;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventorySequence
 * @group model
 */
class CurriculumInventorySequenceTest extends EntityBase
{
    protected function setUp(): void
    {
        $this->object = new CurriculumInventorySequence();
    }

    public function testNotBlankValidation(): void
    {
        $notNull = [
            'report',
        ];
        $this->validateNotNulls($notNull);

        $this->object->setReport(m::mock(CurriculumInventoryReportInterface::class));
        $this->object->setDescription('');
        $this->validate(0);
        $this->object->setDescription('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequence::setDescription
     * @covers \App\Entity\CurriculumInventorySequence::getDescription
     */
    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequence::setReport
     * @covers \App\Entity\CurriculumInventorySequence::getReport
     */
    public function testSetReport(): void
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }
}
