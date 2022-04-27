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
    /**
     * @var Vocabulary
     */
    protected $object;

    /**
     * Instantiate a Vocabulary object
     */
    protected function setUp(): void
    {
        $this->object = new Vocabulary();
    }

    /**
     * @covers \App\Entity\Vocabulary::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getTerms());
    }

    public function testNotEmptyValidation()
    {
        $errors = $this->validate(2);
        $this->assertEquals([
            "title" => "NotBlank",
            "school" => "NotNull",
        ], $errors);

        $this->object->setSchool(m::mock(SchoolInterface::class));
        $this->object->setTitle('Jackson is the best dog!');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Vocabulary::setTitle
     * @covers \App\Entity\Vocabulary::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Vocabulary::setSchool
     * @covers \App\Entity\Vocabulary::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\Vocabulary::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\Vocabulary::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\Vocabulary::getTerms
     */
    public function testGetTerm()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\Vocabulary::setActive
     * @covers \App\Entity\Vocabulary::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }
}
