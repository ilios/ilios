<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Alert;
use Mockery as m;

/**
 * Tests for Model Alert
 */
class AlertTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\Alert::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getChangeTypes());
        $this->assertEmpty($this->object->getInstigators());
        $this->assertEmpty($this->object->getRecipients());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\Alert::getAlertId
     */
    public function testGetAlertId()
    {
        $this->basicGetTest('alertId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::setTableRowId
     */
    public function testSetTableRowId()
    {
        $this->basicSetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::getTableRowId
     */
    public function testGetTableRowId()
    {
        $this->basicGetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::setTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::getTableName
     */
    public function testGetTableName()
    {
        $this->basicGetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::setAdditionalText
     */
    public function testSetAdditionalText()
    {
        $this->basicSetTest('additionalText', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::getAdditionalText
     */
    public function testGetAdditionalText()
    {
        $this->basicGetTest('additionalText', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::setDispatched
     */
    public function testSetDispatched()
    {
        $this->basicSetTest('dispatched', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::getDispatched
     */
    public function testGetDispatched()
    {
        $this->basicGetTest('dispatched', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::addChangeType
     */
    public function testAddChangeType()
    {
        $this->modelCollectionAddTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::removeChangeType
     */
    public function testRemoveChangeType()
    {
        $this->modelCollectionRemoveTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::getChangeTypes
     */
    public function testGetChangeTypes()
    {
        $this->modelCollectionGetTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::addInstigator
     */
    public function testAddInstigator()
    {
        $this->modelCollectionAddTest('instigator', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::removeInstigator
     */
    public function testRemoveInstigator()
    {
        $this->modelCollectionRemoveTest('instigator', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::getInstigators
     */
    public function testGetInstigators()
    {
        $this->modelCollectionGetTest('instigator', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::addRecipient
     */
    public function testAddRecipient()
    {
        $this->modelCollectionAddTest('recipient', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::removeRecipient
     */
    public function testRemoveRecipient()
    {
        $this->modelCollectionRemoveTest('recipient', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Alert::getRecipients
     */
    public function testGetRecipients()
    {
        $this->modelCollectionGetTest('recipient', 'School');
    }
}
