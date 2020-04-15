<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ProgramYearObjective;

/**
 * Tests for Entity ProgramYearObjective
 * @group model
 */
class ProgramYearObjectiveTest extends EntityBase
{
    /**
     * @var ProgramYearObjective
     */
    protected $object;

    /**
     * Instantiate a ProgramYearObjective object
     */
    protected function setUp(): void
    {
        $this->object = new ProgramYearObjective();
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::setProgramYear
     * @covers \App\Entity\ProgramYearObjective::getProgramYear
     */
    public function testSetProgramYear()
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::setPosition
     * @covers \App\Entity\ProgramYearObjective::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }
    /**
     * @covers \App\Entity\ProgramYearObjective::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::getTerms
     * @covers \App\Entity\ProgramYearObjective::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }
}
