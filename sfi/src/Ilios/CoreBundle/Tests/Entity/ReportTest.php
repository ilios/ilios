<?php
namespace Ilios\CoreBundle\Tests\Entity;


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
     * @covers Ilios\CoreBundle\Entity\Report::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::setCreationDate
     */
    public function testSetCreationDate()
    {
        $this->basicSetTest('creationDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::getCreationDate
     */
    public function testGetCreationDate()
    {
        $this->basicGetTest('creationDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::setSubject
     */
    public function testSetSubject()
    {
        $this->basicSetTest('subject', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::getSubject
     */
    public function testGetSubject()
    {
        $this->basicGetTest('subject', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::setPrepositionalObject
     */
    public function testSetPrepositionalObject()
    {
        $this->basicSetTest('prepositionalObject', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::getPrepositionalObject
     */
    public function testGetPrepositionalObject()
    {
        $this->basicGetTest('prepositionalObject', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::setUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Report::getUser
     */
    public function testGetUser()
    {
        $this->entityGetTest('user', 'User');
    }
}
