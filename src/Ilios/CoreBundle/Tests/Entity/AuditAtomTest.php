<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\AuditAtom;
use Mockery as m;

/**
 * Tests for Entity AuditAtom
 */
class AuditAtomTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\AuditAtom::setTableColumn
     * @covers Ilios\CoreBundle\Entity\AuditAtom::getTableColumn
     */
    public function testSetTableColumn()
    {
        $this->basicSetTest('tableColumn', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AuditAtom::setTableName
     * @covers Ilios\CoreBundle\Entity\AuditAtom::getTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AuditAtom::setEventType
     * @covers Ilios\CoreBundle\Entity\AuditAtom::getEventType
     */
    public function testSetEventType()
    {
        $this->basicSetTest('eventType', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AuditAtom::setCreatedAt
     * @covers Ilios\CoreBundle\Entity\AuditAtom::getCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AuditAtom::setCreatedBy
     * @covers Ilios\CoreBundle\Entity\AuditAtom::getCreatedBy
     */
    public function testSetCreatedBy()
    {
        $this->entitySetTest('createdBy', 'User');
    }
}
