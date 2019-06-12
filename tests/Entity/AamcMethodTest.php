<?php
namespace App\Tests\Entity;

use App\Entity\AamcMethod;

/**
 * Tests for Entity AamcMethod
 */
class AamcMethodTest extends EntityBase
{
    /**
     * @var AamcMethod
     */
    protected $object;

    /**
     * Instantiate a AamcMethod object
     */
    protected function setUp()
    {
        $this->object = new AamcMethod;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'id',
            'description'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setId('strtest');
        $this->object->setDescription('my car is great');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\AamcMethod::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getSessionTypes());
    }

    /**
     * @covers \App\Entity\AamcMethod::setDescription
     * @covers \App\Entity\AamcMethod::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\AamcMethod::addSessionType
     */
    public function testAddSessionType()
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType', false, false, 'addAamcMethod');
    }

    /**
     * @covers \App\Entity\AamcMethod::removeSessionType
     */
    public function testRemoveSessionType()
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType', false, false, false, 'removeAamcMethod');
    }

    /**
     * @covers \App\Entity\AamcMethod::getSessionTypes
     */
    public function testGetSessionTypes()
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType', false, false, 'addAamcMethod');
    }

    /**
     * @covers \App\Entity\AamcMethod::setActive
     * @covers \App\Entity\AamcMethod::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }
}
