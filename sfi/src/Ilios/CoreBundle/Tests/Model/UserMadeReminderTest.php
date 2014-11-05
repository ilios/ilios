<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\UserMadeReminder;
use Mockery as m;

/**
 * Tests for Model UserMadeReminder
 */
class UserMadeReminderTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::getUserMadeReminderId
     */
    public function testGetUserMadeReminderId()
    {
        $this->basicGetTest('userMadeReminderId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::setNote
     */
    public function testSetNote()
    {
        $this->basicSetTest('note', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::getNote
     */
    public function testGetNote()
    {
        $this->basicGetTest('note', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::setCreationDate
     */
    public function testSetCreationDate()
    {
        $this->basicSetTest('creationDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::getCreationDate
     */
    public function testGetCreationDate()
    {
        $this->basicGetTest('creationDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::setDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::getDueDate
     */
    public function testGetDueDate()
    {
        $this->basicGetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::setClosed
     */
    public function testSetClosed()
    {
        $this->basicSetTest('closed', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::getClosed
     */
    public function testGetClosed()
    {
        $this->basicGetTest('closed', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::setUser
     */
    public function testSetUser()
    {
        $this->modelSetTest('user', "user");
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserMadeReminder::getUser
     */
    public function testGetUser()
    {
        $this->modelGetTest('user', "user");
    }
}
