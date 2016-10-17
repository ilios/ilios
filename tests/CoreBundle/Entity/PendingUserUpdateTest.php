<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\PendingUserUpdate;
use Ilios\CoreBundle\Entity\User;
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
     * @covers \Ilios\CoreBundle\Entity\PendingUserUpdate::setType
     * @covers \Ilios\CoreBundle\Entity\PendingUserUpdate::getType
     */
    public function testSetType()
    {
        $this->basicSetTest('type', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\PendingUserUpdate::setProperty
     * @covers \Ilios\CoreBundle\Entity\PendingUserUpdate::getProperty
     */
    public function testSetProperty()
    {
        $this->basicSetTest('property', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\PendingUserUpdate::setValue
     * @covers \Ilios\CoreBundle\Entity\PendingUserUpdate::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }
    
    /**
     * @covers \Ilios\CoreBundle\Entity\PendingUserUpdate::setUser
     * @covers \Ilios\CoreBundle\Entity\PendingUserUpdate::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }
}
