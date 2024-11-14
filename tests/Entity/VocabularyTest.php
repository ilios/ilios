<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SchoolInterface;
use App\Entity\Vocabulary;
use Mockery as m;

/**
 * Tests for Entity Vocabulary
 * @group model
 */
class VocabularyTest extends EntityBase
{
    protected Vocabulary $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Vocabulary();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    /**
     * @covers \App\Entity\Vocabulary::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getTerms());
    }

    public function testNotBlankValidation(): void
    {
        $this->object->setSchool(m::mock(SchoolInterface::class));
        $this->validateNotBlanks(['title']);
        $this->object->setTitle('Jackson is the best dog!');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $this->object->setTitle('Jackson is the best dog!');
        $this->validateNotNulls(['school']);
        $this->object->setSchool(m::mock(SchoolInterface::class));
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Vocabulary::setTitle
     * @covers \App\Entity\Vocabulary::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Vocabulary::setSchool
     * @covers \App\Entity\Vocabulary::getSchool
     */
    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\Vocabulary::addTerm
     */
    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\Vocabulary::removeTerm
     */
    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\Vocabulary::getTerms
     */
    public function testGetTerm(): void
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\Vocabulary::setActive
     * @covers \App\Entity\Vocabulary::isActive
     */
    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }

    protected function getObject(): Vocabulary
    {
        return $this->object;
    }
}
