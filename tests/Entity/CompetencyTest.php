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
    /**
     * @var Competency
     */
    protected $object;

    /**
     * Instantiate a Competency object
     */
    protected function setUp(): void
    {
        $this->object = new Competency();
    }

    public function testNotBlankValidation()
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
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAamcPcrses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getChildren());
    }

    /**
     * @covers \App\Entity\Competency::setTitle
     * @covers \App\Entity\Competency::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Competency::setSchool
     * @covers \App\Entity\Competency::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\Competency::setParent
     * @covers \App\Entity\Competency::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'Competency');
    }

    /**
     * @covers \App\Entity\Competency::setParent
     */
    public function testRemoveParent()
    {
        $obj = m::mock('App\Entity\Competency');
        $this->object->setParent($obj);
        $this->assertSame($obj, $this->object->getParent());
        $this->object->setParent(null);
        $this->assertNull($this->object->getParent());
    }

    /**
     * @covers \App\Entity\Competency::addAamcPcrs
     */
    public function testAddPcrs()
    {
        $this->entityCollectionAddTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'addAamcPcrs', 'addCompetency');
    }

    /**
     * @covers \App\Entity\Competency::removeAamcPcrs
     */
    public function testRemovePcrs()
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
    public function testGetPcrses()
    {
        $this->entityCollectionSetTest('aamcPcrses', 'AamcPcrs', 'getAamcPcrses', 'setAamcPcrses', 'addCompetency');
    }

    /**
     * @covers \App\Entity\Competency::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    /**
     * @covers \App\Entity\Competency::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeCompetency');
    }

    /**
     * @covers \App\Entity\Competency::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addCompetency');
    }

    /**
     * @covers \App\Entity\Competency::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'Competency', 'getChildren');
    }

    /**
     * @covers \App\Entity\Competency::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'Competency', 'getChildren');
    }

    /**
     * @covers \App\Entity\Competency::getChildren
     * @covers \App\Entity\Competency::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'Competency', 'getChildren', 'setChildren');
    }

    /**
     * @covers \App\Entity\Competency::setActive
     * @covers \App\Entity\Competency::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }
}
