<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AuditLog;
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
     * @covers \AppBundle\Entity\AuditLog::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers \AppBundle\Entity\AuditLog::setAction
     * @covers \AppBundle\Entity\AuditLog::getAction
     */
    public function testSetAction()
    {
        $this->basicSetTest('action', 'string');
    }

    /**
     * @covers \AppBundle\Entity\AuditLog::setObjectId
     * @covers \AppBundle\Entity\AuditLog::getObjectId
     */
    public function testSetObjectId()
    {
        $this->basicSetTest('objectId', 'integer');
    }

    /**
     * @covers \AppBundle\Entity\AuditLog::setObjectId
     * @covers \AppBundle\Entity\AuditLog::getObjectId
     */
    public function testSetObjectIdString()
    {
        $this->basicSetTest('objectId', 'string');
    }

    /**
     * @covers \AppBundle\Entity\AuditLog::setObjectClass
     * @covers \AppBundle\Entity\AuditLog::getObjectClass
     */
    public function testSetObjectClass()
    {
        $this->basicSetTest('objectClass', 'string');
    }

    /**
     * @covers \AppBundle\Entity\AuditLog::setValuesChanged
     * @covers \AppBundle\Entity\AuditLog::getValuesChanged
     */
    public function testSetValuesChanged()
    {
        $this->basicSetTest('valuesChanged', 'string');
    }

    /**
     * @covers \AppBundle\Entity\AuditLog::setUser
     * @covers \AppBundle\Entity\AuditLog::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \AppBundle\Entity\AuditLog::setCreatedAt
     * @covers \AppBundle\Entity\AuditLog::getCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }
}
