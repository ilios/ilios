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

    /**
     * @covers \App\Entity\Competency::__construct
     */
    public function testConstructor(): void
    {
        $this->assertEmpty($this->object->getAamcPcrses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getChildren());
    }

    /**
     * @covers \App\Entity\Competency::setTitle
     * @covers \App\Entity\Competency::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Competency::setSchool
     * @covers \App\Entity\Competency::getSchool
     */
    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\Competency::setParent
     * @covers \App\Entity\Competency::getParent
     */
    public function testSetParent(): void
    {
        $this->entitySetTest('parent', 'Competency');
    }

    /**
     * @covers \App\Entity\Competency::setParent
     */
    public function testRemoveParent(): void
    {
        $obj = m::mock(Competency::class);
        $this->object->setParent($obj);
        $this->assertSame($obj, $this->object->getParent());
        $this->object->setParent(null);
        $this->assertNull($this->object->getParent());
    }

    /**
     * @covers \App\Entity\Competency::addAamcPcrs
     */
    public function testAddPcrs(): void
    {
        $this->entityCollectionAddTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'addAamcPcrs', 'addCompetency');
    }

    /**
     * @covers \App\Entity\Competency::removeAamcPcrs
     */
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

    /**
     * @covers \App\Entity\Competency::getAamcPcrses
     * @covers \App\Entity\Competency::setAamcPcrses
     */
    public function testGetPcrses(): void
    {
        $this->entityCollectionSetTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'setAamcPcrses', 'addCompetency');
    }

    /**
     * @covers \App\Entity\Competency::addProgramYear
     */
    public function testAddProgramYear(): void
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    /**
     * @covers \App\Entity\Competency::removeProgramYear
     */
    public function testRemoveProgramYear(): void
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeCompetency');
    }

    /**
     * @covers \App\Entity\Competency::getProgramYears
     */
    public function testGetProgramYears(): void
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    /**
     * @covers \App\Entity\Competency::addChild
     */
    public function testAddChild(): void
    {
        $this->entityCollectionAddTest('child', 'Competency', 'getChildren');
    }

    /**
     * @covers \App\Entity\Competency::removeChild
     */
    public function testRemoveChild(): void
    {
        $this->entityCollectionRemoveTest('child', 'Competency', 'getChildren');
    }

    /**
     * @covers \App\Entity\Competency::getChildren
     * @covers \App\Entity\Competency::setChildren
     */
    public function testGetChildren(): void
    {
        $this->entityCollectionSetTest('child', 'Competency', 'getChildren', 'setChildren');
    }

    /**
     * @covers \App\Entity\Competency::setActive
     * @covers \App\Entity\Competency::isActive
     */
    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }

    protected function getObject(): Competency
    {
        return $this->object;
    }
}
