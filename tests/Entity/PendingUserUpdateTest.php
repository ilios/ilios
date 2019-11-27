<?php
namespace App\Tests\Entity;

use App\Entity\PendingUserUpdate;
use App\Entity\User;
use Mockery as m;

/**
 * Tests for Entity PendingUserUpdate
 * @group model
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
     * @covers \App\Entity\PendingUserUpdate::setType
     * @covers \App\Entity\PendingUserUpdate::getType
     */
    public function testSetType()
    {
        $this->basicSetTest('type', 'string');
    }

    /**
     * @covers \App\Entity\PendingUserUpdate::setProperty
     * @covers \App\Entity\PendingUserUpdate::getProperty
     */
    public function testSetProperty()
    {
        $this->basicSetTest('property', 'string');
    }

    /**
     * @covers \App\Entity\PendingUserUpdate::setValue
     * @covers \App\Entity\PendingUserUpdate::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }

    /**
     * @covers \App\Entity\PendingUserUpdate::setUser
     * @covers \App\Entity\PendingUserUpdate::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }
}
