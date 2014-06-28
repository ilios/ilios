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
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getUserMadeReminderId
     */
    public function testGetUserMadeReminderId()
    {
        $this->basicGetTest('userMadeReminderId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setNote
     */
    public function testSetNote()
    {
        $this->basicSetTest('note', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getNote
     */
    public function testGetNote()
    {
        $this->basicGetTest('note', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setCreationDate
     */
    public function testSetCreationDate()
    {
        $this->basicSetTest('creationDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getCreationDate
     */
    public function testGetCreationDate()
    {
        $this->basicGetTest('creationDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getDueDate
     */
    public function testGetDueDate()
    {
        $this->basicGetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setClosed
     */
    public function testSetClosed()
    {
        $this->basicSetTest('closed', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getClosed
     */
    public function testGetClosed()
    {
        $this->basicGetTest('closed', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::setUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', "user");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserMadeReminder::getUser
     */
    public function testGetUser()
    {
        $this->entityGetTest('user', "user");
    }
}
