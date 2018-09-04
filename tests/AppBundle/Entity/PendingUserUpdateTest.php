<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\PendingUserUpdate;
use AppBundle\Entity\User;
use Mockery as m;

/**
 * Tests for Entity PendingUserUpdate
 */
class PendingUserUpdateTest extends EntityBase
{
    /**
     * @var PendingUserUpdate
     */
    protected $object;

    /**
     * Instantiate a PendingUserUpdate object
     */
    protected function setUp()
    {
        $this->object = new PendingUserUpdate;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'type',
            'property',
            'value',
        );
        $this->object->setUser(new User());
        $this->validateNotBlanks($notBlank);

        $this->object->setType('test');
        $this->object->setProperty('test');
        $this->object->setValue('test');
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNull = array(
            'user',
        );

        $this->object->setType('test');
        $this->object->setProperty('test');
        $this->object->setValue('test');
        $this->validateNotNulls($notNull);

        $this->object->setUser(new User());
        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\PendingUserUpdate::setType
     * @covers \AppBundle\Entity\PendingUserUpdate::getType
     */
    public function testSetType()
    {
        $this->basicSetTest('type', 'string');
    }

    /**
     * @covers \AppBundle\Entity\PendingUserUpdate::setProperty
     * @covers \AppBundle\Entity\PendingUserUpdate::getProperty
     */
    public function testSetProperty()
    {
        $this->basicSetTest('property', 'string');
    }

    /**
     * @covers \AppBundle\Entity\PendingUserUpdate::setValue
     * @covers \AppBundle\Entity\PendingUserUpdate::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }
    
    /**
     * @covers \AppBundle\Entity\PendingUserUpdate::setUser
     * @covers \AppBundle\Entity\PendingUserUpdate::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }
}
