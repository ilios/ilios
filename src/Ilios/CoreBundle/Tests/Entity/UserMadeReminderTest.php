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
     */
    public function testSetNote()
    {
        $this->basicSetTest('note', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setClosed
     */
    public function testSetClosed()
    {
        $this->booleanSetTest('closed');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', "user");
    }
}
