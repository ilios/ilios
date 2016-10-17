<?php
namespace Tests\CoreBundle\Entity;

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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'dueDate'
        );
        $this->object->setUser(m::mock('Ilios\CoreBundle\Entity\UserInterface'));

        $this->validateNotBlanks($notBlank);

        $this->object->setDueDate(new \DateTime());
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNulls = array(
            'user'
        );
        $this->object->setDueDate(new \DateTime());

        $this->validateNotNulls($notNulls);

        $this->object->setUser(m::mock('Ilios\CoreBundle\Entity\UserInterface'));
        $this->validate(0);
    }
    
    /**
     * @covers \Ilios\CoreBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }
    
    /**
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::setNote
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::getNote
     */
    public function testSetNote()
    {
        $this->basicSetTest('note', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::setDueDate
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::getDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::setClosed
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::isClosed
     */
    public function testSetClosed()
    {
        $this->booleanSetTest('closed');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::setUser
     * @covers \Ilios\CoreBundle\Entity\UserMadeReminder::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', "user");
    }
}
