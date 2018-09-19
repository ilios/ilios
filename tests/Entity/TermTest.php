<?php
namespace Tests\App\Entity;

use App\Entity\Term;
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
     * @covers \App\Entity\Term::__construct
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
     * @covers \App\Entity\Term::setTitle
     * @covers \App\Entity\Term::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Term::setDescription
     * @covers \App\Entity\Term::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\Term::setVocabulary
     * @covers \App\Entity\Term::getVocabulary
     */
    public function testSetVocabulary()
    {
        $this->entitySetTest('vocabulary', 'Vocabulary');
    }

    /**
     * @covers \App\Entity\Term::setParent
     * @covers \App\Entity\Term::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'Term');
    }

    /**
     * @covers \App\Entity\Term::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeTerm');
    }

    /**
     * @covers \App\Entity\Term::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeTerm');
    }

    /**
     * @covers \App\Entity\Term::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeTerm');
    }

    /**
     * @covers \App\Entity\Term::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addTerm');
    }

    /**
     * @covers \App\Entity\Term::addAamcResourceType
     */
    public function testAddAamcResourceTypes()
    {
        $this->entityCollectionAddTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \App\Entity\Term::removeAamcResourceType
     */
    public function testRemoveAamcResourceTypes()
    {
        $this->entityCollectionRemoveTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \App\Entity\Term::getAamcResourceTypes
     * @covers \App\Entity\Term::setAamcResourceTypes
     */
    public function testGetAamcResourceTypes()
    {
        $this->entityCollectionSetTest('aamcResourceType', 'AamcResourceType');
    }

    /**
     * @covers \App\Entity\Term::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'Term', 'getChildren');
    }

    /**
     * @covers \App\Entity\Term::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'Term', 'getChildren');
    }

    /**
     * @covers \AppBundle\Entity\Term::getChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'Term', 'getChildren', 'setChildren');
    }

    /**
     * @covers \App\Entity\Term::setActive
     * @covers \App\Entity\Term::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }
}
