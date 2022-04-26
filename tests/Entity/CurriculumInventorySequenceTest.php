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
    /**
     * @var CurriculumInventorySequence
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventorySequence object
     */
    protected function setUp(): void
    {
        $this->object = new CurriculumInventorySequence();
    }

    public function testNotBlankValidation()
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
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequence::setReport
     * @covers \App\Entity\CurriculumInventorySequence::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }
}
