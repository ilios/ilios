<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Alert;
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
     * @covers \AppBundle\Entity\Alert::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getChangeTypes());
        $this->assertEmpty($this->object->getInstigators());
        $this->assertEmpty($this->object->getRecipients());
    }

    /**
     * @covers \AppBundle\Entity\Alert::setTableName
     * @covers \AppBundle\Entity\Alert::getTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Alert::setAdditionalText
     * @covers \AppBundle\Entity\Alert::getAdditionalText
     */
    public function testSetAdditionalText()
    {
        $this->basicSetTest('additionalText', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Alert::setDispatched
     * @covers \AppBundle\Entity\Alert::isDispatched
     */
    public function testSetDispatched()
    {
        $this->booleanSetTest('dispatched');
    }

    /**
     * @covers \AppBundle\Entity\Alert::addChangeType
     */
    public function testAddChangeType()
    {
        $this->entityCollectionAddTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \AppBundle\Entity\Alert::removeChangeType
     */
    public function testRemoveChangeType()
    {
        $this->entityCollectionRemoveTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \AppBundle\Entity\Alert::getChangeTypes
     * @covers \AppBundle\Entity\Alert::setChangeTypes
     */
    public function testGetChangeTypes()
    {
        $this->entityCollectionSetTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \AppBundle\Entity\Alert::addInstigator
     */
    public function testAddInstigator()
    {
        $this->entityCollectionAddTest('instigator', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Alert::removeInstigator
     */
    public function testRemoveInstigator()
    {
        $this->entityCollectionRemoveTest('instigator', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Alert::getInstigators
     * @covers \AppBundle\Entity\Alert::setInstigators
     */
    public function testGetInstigators()
    {
        $this->entityCollectionSetTest('instigator', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Alert::addRecipient
     */
    public function testAddRecipient()
    {
        $this->entityCollectionAddTest('recipient', 'School');
    }

    /**
     * @covers \AppBundle\Entity\Alert::removeRecipient
     */
    public function testRemoveRecipient()
    {
        $this->entityCollectionRemoveTest('recipient', 'School');
    }

    /**
     * @covers \AppBundle\Entity\Alert::getRecipients
     * @covers \AppBundle\Entity\Alert::setRecipients
     */
    public function testGetRecipients()
    {
        $this->entityCollectionSetTest('recipient', 'School');
    }
}
