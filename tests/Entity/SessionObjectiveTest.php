<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SessionObjective;

/**
 * Tests for Entity SessionObjective
 * @group model
 */
class SessionObjectiveTest extends EntityBase
{
    /**
     * @var SessionObjective
     */
    protected $object;

    /**
     * Instantiate a SessionObjective object
     */
    protected function setUp(): void
    {
        $this->object = new SessionObjective();
    }

    /**
     * @covers \App\Entity\SessionObjective::setSession
     * @covers \App\Entity\SessionObjective::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\SessionObjective::setPosition
     * @covers \App\Entity\SessionObjective::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }
    /**
     * @covers \App\Entity\SessionObjective::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\SessionObjective::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\SessionObjective::getTerms
     * @covers \App\Entity\SessionObjective::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }
}
