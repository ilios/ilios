<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\AuditLog;
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
        $this->object->setObjectId(1);
        $this->object->setObjectClass('test');
        $this->object->setValuesChanged('test');
        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AuditLog::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AuditLog::setAction
     * @covers \Ilios\CoreBundle\Entity\AuditLog::getAction
     */
    public function testSetAction()
    {
        $this->basicSetTest('action', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AuditLog::setObjectId
     * @covers \Ilios\CoreBundle\Entity\AuditLog::getObjectId
     */
    public function testSetObjectId()
    {
        $this->basicSetTest('objectId', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AuditLog::setObjectId
     * @covers \Ilios\CoreBundle\Entity\AuditLog::getObjectId
     */
    public function testSetObjectIdForcesInt()
    {
        $this->object->setObjectId('');
        $this->assertSame(0, $this->object->getObjectId());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AuditLog::setObjectClass
     * @covers \Ilios\CoreBundle\Entity\AuditLog::getObjectClass
     */
    public function testSetObjectClass()
    {
        $this->basicSetTest('objectClass', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AuditLog::setValuesChanged
     * @covers \Ilios\CoreBundle\Entity\AuditLog::getValuesChanged
     */
    public function testSetValuesChanged()
    {
        $this->basicSetTest('valuesChanged', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AuditLog::setUser
     * @covers \Ilios\CoreBundle\Entity\AuditLog::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::setCreatedAt
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::getCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }
}
