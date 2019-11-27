<?php
namespace App\Tests\Entity;

use App\Entity\Alert;
use Mockery as m;

/**
 * Tests for Entity Alert
 * @group model
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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'tableRowId',
            'tableName'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTableRowId(3215);
        $this->object->setTableName('zippeedee doo dah');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Alert::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getChangeTypes());
        $this->assertEmpty($this->object->getInstigators());
        $this->assertEmpty($this->object->getRecipients());
    }

    /**
     * @covers \App\Entity\Alert::setTableName
     * @covers \App\Entity\Alert::getTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers \App\Entity\Alert::setAdditionalText
     * @covers \App\Entity\Alert::getAdditionalText
     */
    public function testSetAdditionalText()
    {
        $this->basicSetTest('additionalText', 'string');
    }

    /**
     * @covers \App\Entity\Alert::setDispatched
     * @covers \App\Entity\Alert::isDispatched
     */
    public function testSetDispatched()
    {
        $this->booleanSetTest('dispatched');
    }

    /**
     * @covers \App\Entity\Alert::addChangeType
     */
    public function testAddChangeType()
    {
        $this->entityCollectionAddTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \App\Entity\Alert::removeChangeType
     */
    public function testRemoveChangeType()
    {
        $this->entityCollectionRemoveTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \App\Entity\Alert::getChangeTypes
     * @covers \App\Entity\Alert::setChangeTypes
     */
    public function testGetChangeTypes()
    {
        $this->entityCollectionSetTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \App\Entity\Alert::addInstigator
     */
    public function testAddInstigator()
    {
        $this->entityCollectionAddTest('instigator', 'User');
    }

    /**
     * @covers \App\Entity\Alert::removeInstigator
     */
    public function testRemoveInstigator()
    {
        $this->entityCollectionRemoveTest('instigator', 'User');
    }

    /**
     * @covers \App\Entity\Alert::getInstigators
     * @covers \App\Entity\Alert::setInstigators
     */
    public function testGetInstigators()
    {
        $this->entityCollectionSetTest('instigator', 'User');
    }

    /**
     * @covers \App\Entity\Alert::addRecipient
     */
    public function testAddRecipient()
    {
        $this->entityCollectionAddTest('recipient', 'School');
    }

    /**
     * @covers \App\Entity\Alert::removeRecipient
     */
    public function testRemoveRecipient()
    {
        $this->entityCollectionRemoveTest('recipient', 'School');
    }

    /**
     * @covers \App\Entity\Alert::getRecipients
     * @covers \App\Entity\Alert::setRecipients
     */
    public function testGetRecipients()
    {
        $this->entityCollectionSetTest('recipient', 'School');
    }
}
