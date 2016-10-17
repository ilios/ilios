<?php
namespace Tests\CoreBundle\Entity;

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
     * @covers \Ilios\CoreBundle\Entity\Alert::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getChangeTypes());
        $this->assertEmpty($this->object->getInstigators());
        $this->assertEmpty($this->object->getRecipients());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::setTableName
     * @covers \Ilios\CoreBundle\Entity\Alert::getTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::setAdditionalText
     * @covers \Ilios\CoreBundle\Entity\Alert::getAdditionalText
     */
    public function testSetAdditionalText()
    {
        $this->basicSetTest('additionalText', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::setDispatched
     * @covers \Ilios\CoreBundle\Entity\Alert::isDispatched
     */
    public function testSetDispatched()
    {
        $this->booleanSetTest('dispatched');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::addChangeType
     */
    public function testAddChangeType()
    {
        $this->entityCollectionAddTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::removeChangeType
     */
    public function testRemoveChangeType()
    {
        $this->entityCollectionRemoveTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::getChangeTypes
     * @covers \Ilios\CoreBundle\Entity\Alert::setChangeTypes
     */
    public function testGetChangeTypes()
    {
        $this->entityCollectionSetTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::addInstigator
     */
    public function testAddInstigator()
    {
        $this->entityCollectionAddTest('instigator', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::removeInstigator
     */
    public function testRemoveInstigator()
    {
        $this->entityCollectionRemoveTest('instigator', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::getInstigators
     * @covers \Ilios\CoreBundle\Entity\Alert::setInstigators
     */
    public function testGetInstigators()
    {
        $this->entityCollectionSetTest('instigator', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::addRecipient
     */
    public function testAddRecipient()
    {
        $this->entityCollectionAddTest('recipient', 'School');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::removeRecipient
     */
    public function testRemoveRecipient()
    {
        $this->entityCollectionRemoveTest('recipient', 'School');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Alert::getRecipients
     * @covers \Ilios\CoreBundle\Entity\Alert::setRecipients
     */
    public function testGetRecipients()
    {
        $this->entityCollectionSetTest('recipient', 'School');
    }
}
