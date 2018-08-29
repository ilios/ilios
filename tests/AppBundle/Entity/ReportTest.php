<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Report;
use Mockery as m;

/**
 * Tests for Entity Report
 */
class ReportTest extends EntityBase
{
    /**
     * @var Report
     */
    protected $object;

    /**
     * Instantiate a Report object
     */
    protected function setUp()
    {
        $this->object = new Report;
    }
    
    /**
     * @covers \AppBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers \AppBundle\Entity\Report::setSubject
     * @covers \AppBundle\Entity\Report::getSubject
     */
    public function testSetSubject()
    {
        $this->basicSetTest('subject', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Report::setPrepositionalObject
     * @covers \AppBundle\Entity\Report::getPrepositionalObject
     */
    public function testSetPrepositionalObject()
    {
        $this->basicSetTest('prepositionalObject', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Report::setPrepositionalObjectTableRowId
     * @covers \AppBundle\Entity\Report::getPrepositionalObjectTableRowId
     */
    public function testSetPrepositionalObjectTableRowId()
    {
        $this->basicSetTest('prepositionalObjectTableRowId', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Report::setTitle
     * @covers \AppBundle\Entity\Report::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Report::setUser
     * @covers \AppBundle\Entity\Report::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Report::setSchool
     * @covers \AppBundle\Entity\Report::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
