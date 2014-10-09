<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Report;
use Mockery as m;

/**
 * Tests for Model Report
 */
class ReportTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\Report::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::setCreationDate
     */
    public function testSetCreationDate()
    {
        $this->basicSetTest('creationDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::getCreationDate
     */
    public function testGetCreationDate()
    {
        $this->basicGetTest('creationDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::setSubject
     */
    public function testSetSubject()
    {
        $this->basicSetTest('subject', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::getSubject
     */
    public function testGetSubject()
    {
        $this->basicGetTest('subject', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::setPrepositionalObject
     */
    public function testSetPrepositionalObject()
    {
        $this->basicSetTest('prepositionalObject', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::getPrepositionalObject
     */
    public function testGetPrepositionalObject()
    {
        $this->basicGetTest('prepositionalObject', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::setUser
     */
    public function testSetUser()
    {
        $this->modelSetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Report::getUser
     */
    public function testGetUser()
    {
        $this->modelGetTest('user', 'User');
    }
}
