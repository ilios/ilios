<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AamcMethod;

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
     * @covers \AppBundle\Entity\AamcMethod::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getSessionTypes());
    }

    /**
     * @covers \AppBundle\Entity\AamcMethod::setDescription
     * @covers \AppBundle\Entity\AamcMethod::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \AppBundle\Entity\AamcMethod::addSessionType
     */
    public function testAddSessionType()
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType', false, false, 'addAamcMethod');
    }

    /**
     * @covers \AppBundle\Entity\AamcMethod::removeSessionType
     */
    public function testRemoveSessionType()
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType', false, false, false, 'removeAamcMethod');
    }

    /**
     * @covers \AppBundle\Entity\AamcMethod::getSessionTypes
     */
    public function testGetSessionTypes()
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType', false, false, 'addAamcMethod');
    }
}
