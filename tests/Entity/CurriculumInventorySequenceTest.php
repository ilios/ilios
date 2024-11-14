<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequence;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventorySequence
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\CurriculumInventorySequence::class)]
class CurriculumInventorySequenceTest extends EntityBase
{
    protected CurriculumInventorySequence $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new CurriculumInventorySequence();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
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

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testSetReport(): void
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    protected function getObject(): CurriculumInventorySequence
    {
        return $this->object;
    }
}
