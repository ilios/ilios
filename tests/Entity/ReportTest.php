<?php
namespace App\Tests\Entity;

use App\Entity\Report;
use Mockery as m;

/**
 * Tests for Entity Report
 * @group model
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
     * @covers \App\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers \App\Entity\Report::setSubject
     * @covers \App\Entity\Report::getSubject
     */
    public function testSetSubject()
    {
        $this->basicSetTest('subject', 'string');
    }

    /**
     * @covers \App\Entity\Report::setPrepositionalObject
     * @covers \App\Entity\Report::getPrepositionalObject
     */
    public function testSetPrepositionalObject()
    {
        $this->basicSetTest('prepositionalObject', 'string');
    }

    /**
     * @covers \App\Entity\Report::setPrepositionalObjectTableRowId
     * @covers \App\Entity\Report::getPrepositionalObjectTableRowId
     */
    public function testSetPrepositionalObjectTableRowId()
    {
        $this->basicSetTest('prepositionalObjectTableRowId', 'string');
    }

    /**
     * @covers \App\Entity\Report::setTitle
     * @covers \App\Entity\Report::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Report::setUser
     * @covers \App\Entity\Report::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\Report::setSchool
     * @covers \App\Entity\Report::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
