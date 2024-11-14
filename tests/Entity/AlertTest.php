<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Alert;

/**
 * Tests for Entity Alert
 * @group model
 */
class AlertTest extends EntityBase
{
    protected Alert $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Alert();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'tableRowId',
            'tableName',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTableRowId(3215);
        $this->object->setTableName('zippeedee doo dah');
        $this->object->setAdditionalText('');
        $this->validate(0);
        $this->object->setAdditionalText('test');
        $this->validate(0);
        $this->object->setAdditionalText(null);
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Alert::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getChangeTypes());
        $this->assertCount(0, $this->object->getInstigators());
        $this->assertCount(0, $this->object->getRecipients());
    }

    /**
     * @covers \App\Entity\Alert::setTableName
     * @covers \App\Entity\Alert::getTableName
     */
    public function testSetTableName(): void
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers \App\Entity\Alert::setAdditionalText
     * @covers \App\Entity\Alert::getAdditionalText
     */
    public function testSetAdditionalText(): void
    {
        $this->basicSetTest('additionalText', 'string');
    }

    /**
     * @covers \App\Entity\Alert::setDispatched
     * @covers \App\Entity\Alert::isDispatched
     */
    public function testSetDispatched(): void
    {
        $this->booleanSetTest('dispatched');
    }

    /**
     * @covers \App\Entity\Alert::addChangeType
     */
    public function testAddChangeType(): void
    {
        $this->entityCollectionAddTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \App\Entity\Alert::removeChangeType
     */
    public function testRemoveChangeType(): void
    {
        $this->entityCollectionRemoveTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \App\Entity\Alert::getChangeTypes
     * @covers \App\Entity\Alert::setChangeTypes
     */
    public function testGetChangeTypes(): void
    {
        $this->entityCollectionSetTest('changeType', 'AlertChangeType');
    }

    /**
     * @covers \App\Entity\Alert::addInstigator
     */
    public function testAddInstigator(): void
    {
        $this->entityCollectionAddTest('instigator', 'User');
    }

    /**
     * @covers \App\Entity\Alert::removeInstigator
     */
    public function testRemoveInstigator(): void
    {
        $this->entityCollectionRemoveTest('instigator', 'User');
    }

    /**
     * @covers \App\Entity\Alert::getInstigators
     * @covers \App\Entity\Alert::setInstigators
     */
    public function testGetInstigators(): void
    {
        $this->entityCollectionSetTest('instigator', 'User');
    }

    /**
     * @covers \App\Entity\Alert::addRecipient
     */
    public function testAddRecipient(): void
    {
        $this->entityCollectionAddTest('recipient', 'School');
    }

    /**
     * @covers \App\Entity\Alert::removeRecipient
     */
    public function testRemoveRecipient(): void
    {
        $this->entityCollectionRemoveTest('recipient', 'School');
    }

    /**
     * @covers \App\Entity\Alert::getRecipients
     * @covers \App\Entity\Alert::setRecipients
     */
    public function testGetRecipients(): void
    {
        $this->entityCollectionSetTest('recipient', 'School');
    }

    protected function getObject(): Alert
    {
        return $this->object;
    }
}
