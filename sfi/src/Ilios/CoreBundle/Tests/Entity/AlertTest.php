<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Alert;
use Mockery as m;

/**
 * Tests for Entity Alert
 */
class AlertTest extends EntityBase
{
    /**
     * @var Alert
     */
    protected $object;

    /**
     * Instantiate a Alert object
     */
    protected function setUp()
    {
        $this->object = new Alert;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getChangeTypes());
        $this->assertEmpty($this->object->getInstigators());
        $this->assertEmpty($this->object->getRecipients());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::setTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::setAdditionalText
     */
    public function testSetAdditionalText()
    {
        $this->basicSetTest('additionalText', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::setDispatched
     */
    public function testSetDispatched()
    {
        $this->booleanSetTest('dispatched');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::addChangeType
     */
    public function testAddChangeType()
    {
        $this->entityCollectionAddTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::removeChangeType
     */
    public function testRemoveChangeType()
    {
        $this->entityCollectionRemoveTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::getChangeTypes
     */
    public function testGetChangeTypes()
    {
        $this->entityCollectionSetTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::addInstigator
     */
    public function testAddInstigator()
    {
        $this->entityCollectionAddTest('instigator', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::removeInstigator
     */
    public function testRemoveInstigator()
    {
        $this->entityCollectionRemoveTest('instigator', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::getInstigators
     */
    public function testGetInstigators()
    {
        $this->entityCollectionSetTest('instigator', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::addRecipient
     */
    public function testAddRecipient()
    {
        $this->entityCollectionAddTest('recipient', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::removeRecipient
     */
    public function testRemoveRecipient()
    {
        $this->entityCollectionRemoveTest('recipient', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::getRecipients
     */
    public function testGetRecipients()
    {
        $this->entityCollectionSetTest('recipient', 'School');
    }
}
