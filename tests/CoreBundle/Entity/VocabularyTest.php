<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Vocabulary;
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
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getTerms());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::setTitle
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::setSchool
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::getTerms
     */
    public function testGetTerm()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::setActive
     * @covers \Ilios\CoreBundle\Entity\Vocabulary::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }
}
