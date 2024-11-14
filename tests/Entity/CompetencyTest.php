<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Competency;
use App\Entity\SchoolInterface;
use Mockery as m;

/**
 * Tests for Entity Competency
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\Competency::class)]
class CompetencyTest extends EntityBase
{
    protected Competency $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Competency();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notNull = [
            'school',
        ];
        $this->validateNotNulls($notNull);

        $this->object->setSchool(m::mock(SchoolInterface::class));
        $this->object->setTitle('');
        $this->validate(0);
        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getAamcPcrses());
        $this->assertCount(0, $this->object->getProgramYears());
        $this->assertCount(0, $this->object->getChildren());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    public function testSetParent(): void
    {
        $this->entitySetTest('parent', 'Competency');
    }

    public function testRemoveParent(): void
    {
        $obj = m::mock(Competency::class);
        $this->object->setParent($obj);
        $this->assertSame($obj, $this->object->getParent());
        $this->object->setParent(null);
        $this->assertNull($this->object->getParent());
    }

    public function testAddPcrs(): void
    {
        $this->entityCollectionAddTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'addAamcPcrs', 'addCompetency');
    }

    public function testRemovePcrs(): void
    {
        $this->entityCollectionRemoveTest(
            'aamcPcrses',
            'AamcPcrs',
            'getAamcPcrses',
            'addAamcPcrs',
            'removeAamcPcrs',
            'removeCompetency'
        );
    }

    public function testGetPcrses(): void
    {
        $this->entityCollectionSetTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'setAamcPcrses', 'addCompetency');
    }

    public function testAddProgramYear(): void
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    public function testRemoveProgramYear(): void
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeCompetency');
    }

    public function testGetProgramYears(): void
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    public function testAddChild(): void
    {
        $this->entityCollectionAddTest('child', 'Competency', 'getChildren');
    }

    public function testRemoveChild(): void
    {
        $this->entityCollectionRemoveTest('child', 'Competency', 'getChildren');
    }

    public function testGetChildren(): void
    {
        $this->entityCollectionSetTest('child', 'Competency', 'getChildren', 'setChildren');
    }

    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }

    protected function getObject(): Competency
    {
        return $this->object;
    }
}
