<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\AuditAtom;
use Mockery as m;

/**
 * Tests for Model AuditAtom
 */
class AuditAtomTest extends ModelBase
{
    /**
     * @var AuditAtom
     */
    protected $object;

    /**
     * Instantiate a AuditAtom object
     */
    protected function setUp()
    {
        $this->object = new AuditAtom;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::getAuditAtomId
     */
    public function testGetAuditAtomId()
    {
        $this->basicGetTest('auditAtomId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::setTableRowId
     */
    public function testSetTableRowId()
    {
        $this->basicSetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::getTableRowId
     */
    public function testGetTableRowId()
    {
        $this->basicGetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::setTableColumn
     */
    public function testSetTableColumn()
    {
        $this->basicSetTest('tableColumn', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::getTableColumn
     */
    public function testGetTableColumn()
    {
        $this->basicGetTest('tableColumn', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::setTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::getTableName
     */
    public function testGetTableName()
    {
        $this->basicGetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::setEventType
     */
    public function testSetEventType()
    {
        $this->basicSetTest('eventType', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::getEventType
     */
    public function testGetEventType()
    {
        $this->basicGetTest('eventType', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::setCreatedBy
     */
    public function testSetCreatedBy()
    {
        $this->modelSetTest('createdBy', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AuditAtom::getCreatedBy
     */
    public function testGetCreatedBy()
    {
        $this->modelGetTest('createdBy', 'User');
    }
}
