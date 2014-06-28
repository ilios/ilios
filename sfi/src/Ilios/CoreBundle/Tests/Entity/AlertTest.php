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
     * @covers Ilios\CoreBundle\Entity\Alert::getAlertId
     */
    public function testGetAlertId()
    {
        $this->basicGetTest('alertId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::setTableRowId
     */
    public function testSetTableRowId()
    {
        $this->basicSetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::getTableRowId
     */
    public function testGetTableRowId()
    {
        $this->basicGetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::setTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::getTableName
     */
    public function testGetTableName()
    {
        $this->basicGetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::setAdditionalText
     */
    public function testSetAdditionalText()
    {
        $this->basicSetTest('additionalText', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::getAdditionalText
     */
    public function testGetAdditionalText()
    {
        $this->basicGetTest('additionalText', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::setDispatched
     */
    public function testSetDispatched()
    {
        $this->basicSetTest('dispatched', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Alert::getDispatched
     */
    public function testGetDispatched()
    {
        $this->basicGetTest('dispatched', 'boolean');
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
        $this->entityCollectionGetTest('changeType', 'AlertChangeType');
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
        $this->entityCollectionGetTest('instigator', 'User');
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
        $this->entityCollectionGetTest('recipient', 'School');
    }
}
