<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\Program;
use App\Entity\SchoolInterface;
use Mockery as m;

/**
 * Tests for Entity Program
 */
#[Group('model')]
#[CoversClass(Program::class)]
final class ProgramTest extends EntityBase
{
    protected Program $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Program();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
            'duration',
        ];
        $this->object->setSchool(m::mock(SchoolInterface::class));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('DVc');
        $this->object->setDuration(30);
        $this->object->setShortTitle('');
        $this->validate(0);
        $this->object->setShortTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNull = [
            'school',
        ];
        $this->object->setTitle('DVc');
        $this->object->setDuration(30);

        $this->validateNotNulls($notNull);

        $this->object->setSchool(m::mock(SchoolInterface::class));

        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getProgramYears());
        $this->assertCount(0, $this->object->getCurriculumInventoryReports());
        $this->assertCount(0, $this->object->getDirectors());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetShortTitle(): void
    {
        $this->basicSetTest('shortTitle', 'string');
    }

    public function testSetDuration(): void
    {
        $this->basicSetTest('duration', 'integer');
    }

    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    public function testAddProgramYear(): void
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    public function testRemoveProgramYear(): void
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear');
    }

    public function testGetProgramYears(): void
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    public function testAddCurriculumInventoryReport(): void
    {
        $this->entityCollectionAddTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    public function testRemoveCurriculumInventoryReport(): void
    {
        $this->entityCollectionRemoveTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    public function testGetCurriculumInventoryReports(): void
    {
        $this->entityCollectionSetTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    public function testAddDirector(): void
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedProgram');
    }

    public function testRemoveDirector(): void
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedProgram');
    }

    public function testGetDirectors(): void
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedProgram');
    }

    protected function getObject(): Program
    {
        return $this->object;
    }
}
