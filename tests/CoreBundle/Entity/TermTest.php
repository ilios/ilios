<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Term;
use Mockery as m;

/**
 * Tests for Entity Term
 */
class TermTest extends EntityBase
{
    /**
     * @var Term
     */
    protected $object;

    /**
     * Instantiate a Term object
     */
    protected function setUp()
    {
        $this->object = new Term;
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getSessions());
        $this->assertEmpty($this->object->getChildren());
        $this->assertEmpty($this->object->getAamcResourceTypes());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::setTitle
     * @covers \Ilios\CoreBundle\Entity\Term::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::setDescription
     * @covers \Ilios\CoreBundle\Entity\Term::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::setVocabulary
     * @covers \Ilios\CoreBundle\Entity\Term::getVocabulary
     */
    public function testSetVocabulary()
    {
        $this->entitySetTest('vocabulary', 'Vocabulary');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::setParent
     * @covers \Ilios\CoreBundle\Entity\Term::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addTerm');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeTerm');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addTerm');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeTerm');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addTerm');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeTerm');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addTerm');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::addAamcResourceType
     */
    public function testAddAamcResourceTypes()
    {
        $this->entityCollectionAddTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::removeAamcResourceType
     */
    public function testRemoveAamcResourceTypes()
    {
        $this->entityCollectionRemoveTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::getAamcResourceTypes
     * @covers \Ilios\CoreBundle\Entity\Term::setAamcResourceTypes
     */
    public function testGetAamcResourceTypes()
    {
        $this->entityCollectionSetTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'Term', 'getChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'Term', 'getChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::getChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'Term', 'getChildren', 'setChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Term::setActive
     * @covers \Ilios\CoreBundle\Entity\Term::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }
}
