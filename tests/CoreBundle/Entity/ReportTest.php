<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Report;
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
     * @covers \Ilios\CoreBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Report::setSubject
     * @covers \Ilios\CoreBundle\Entity\Report::getSubject
     */
    public function testSetSubject()
    {
        $this->basicSetTest('subject', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Report::setPrepositionalObject
     * @covers \Ilios\CoreBundle\Entity\Report::getPrepositionalObject
     */
    public function testSetPrepositionalObject()
    {
        $this->basicSetTest('prepositionalObject', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Report::setPrepositionalObjectTableRowId
     * @covers \Ilios\CoreBundle\Entity\Report::getPrepositionalObjectTableRowId
     */
    public function testSetPrepositionalObjectTableRowId()
    {
        $this->basicSetTest('prepositionalObjectTableRowId', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Report::setTitle
     * @covers \Ilios\CoreBundle\Entity\Report::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Report::setUser
     * @covers \Ilios\CoreBundle\Entity\Report::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Report::setSchool
     * @covers \Ilios\CoreBundle\Entity\Report::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
