<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\UserMadeReminder;
use Mockery as m;

/**
 * Tests for Entity UserMadeReminder
 */
class UserMadeReminderTest extends EntityBase
{
    /**
     * @var UserMadeReminder
     */
    protected $object;

    /**
     * Instantiate a UserMadeReminder object
     */
    protected function setUp()
    {
        $this->object = new UserMadeReminder;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setNote
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getNote
     */
    public function testSetNote()
    {
        $this->basicSetTest('note', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setCreatedAt
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setDueDate
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setClosed
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::isClosed
     */
    public function testSetClosed()
    {
        $this->booleanSetTest('closed');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setUser
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', "user");
    }
}
