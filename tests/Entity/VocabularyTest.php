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
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\Vocabulary::class)]
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

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    public function testGetTerm(): void
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }

    protected function getObject(): Vocabulary
    {
        return $this->object;
    }
}
