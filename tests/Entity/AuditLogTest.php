<?php
namespace Tests\App\Entity;

use App\Entity\AuditLog;
use Mockery as m;

/**
 * Tests for Entity AuditLog
 */
class AuditLogTest extends EntityBase
{
    /**
     * @var AuditLog
     */
    protected $object;

    /**
     * Instantiate a AuditLog object
     */
    protected function setUp()
    {
        $this->object = new AuditLog;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'action',
            'objectId',
            'objectClass',
            'valuesChanged'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setAction('test');
        $this->object->setObjectId('1');
        $this->object->setObjectClass('test');
        $this->object->setValuesChanged('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\AuditLog::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers \App\Entity\AuditLog::setAction
     * @covers \App\Entity\AuditLog::getAction
     */
    public function testSetAction()
    {
        $this->basicSetTest('action', 'string');
    }

    /**
     * @covers \App\Entity\AuditLog::setObjectId
     * @covers \App\Entity\AuditLog::getObjectId
     */
    public function testSetObjectId()
    {
        $this->basicSetTest('objectId', 'integer');
    }

    /**
     * @covers \App\Entity\AuditLog::setObjectId
     * @covers \App\Entity\AuditLog::getObjectId
     */
    public function testSetObjectIdString()
    {
        $this->basicSetTest('objectId', 'string');
    }

    /**
     * @covers \App\Entity\AuditLog::setObjectClass
     * @covers \App\Entity\AuditLog::getObjectClass
     */
    public function testSetObjectClass()
    {
        $this->basicSetTest('objectClass', 'string');
    }

    /**
     * @covers \App\Entity\AuditLog::setValuesChanged
     * @covers \App\Entity\AuditLog::getValuesChanged
     */
    public function testSetValuesChanged()
    {
        $this->basicSetTest('valuesChanged', 'string');
    }

    /**
     * @covers \App\Entity\AuditLog::setUser
     * @covers \App\Entity\AuditLog::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\AuditLog::setCreatedAt
     * @covers \App\Entity\AuditLog::getCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }
}
