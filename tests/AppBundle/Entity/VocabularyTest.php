<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Vocabulary;
use Mockery as m;

/**
 * Tests for Entity Vocabulary
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
    protected function setUp()
    {
        $this->object = new Vocabulary();
    }

    /**
     * @covers \AppBundle\Entity\Vocabulary::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getTerms());
    }

    /**
     * @covers \AppBundle\Entity\Vocabulary::setTitle
     * @covers \AppBundle\Entity\Vocabulary::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Vocabulary::setSchool
     * @covers \AppBundle\Entity\Vocabulary::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \AppBundle\Entity\Vocabulary::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \AppBundle\Entity\Vocabulary::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \AppBundle\Entity\Vocabulary::getTerms
     */
    public function testGetTerm()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \AppBundle\Entity\Vocabulary::setActive
     * @covers \AppBundle\Entity\Vocabulary::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }
}
