<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\Alert;

/**
 * Tests for Entity Alert
 */
#[Group('model')]
#[CoversClass(Alert::class)]
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

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getChangeTypes());
        $this->assertCount(0, $this->object->getInstigators());
        $this->assertCount(0, $this->object->getRecipients());
    }

    public function testSetTableName(): void
    {
        $this->basicSetTest('tableName', 'string');
    }

    public function testSetAdditionalText(): void
    {
        $this->basicSetTest('additionalText', 'string');
    }

    public function testSetDispatched(): void
    {
        $this->booleanSetTest('dispatched');
    }

    public function testAddChangeType(): void
    {
        $this->entityCollectionAddTest('changeType', 'AlertChangeType');
    }

    public function testRemoveChangeType(): void
    {
        $this->entityCollectionRemoveTest('changeType', 'AlertChangeType');
    }

    public function testGetChangeTypes(): void
    {
        $this->entityCollectionSetTest('changeType', 'AlertChangeType');
    }

    public function testAddInstigator(): void
    {
        $this->entityCollectionAddTest('instigator', 'User');
    }

    public function testRemoveInstigator(): void
    {
        $this->entityCollectionRemoveTest('instigator', 'User');
    }

    public function testGetInstigators(): void
    {
        $this->entityCollectionSetTest('instigator', 'User');
    }

    public function testAddRecipient(): void
    {
        $this->entityCollectionAddTest('recipient', 'School');
    }

    public function testRemoveRecipient(): void
    {
        $this->entityCollectionRemoveTest('recipient', 'School');
    }

    public function testGetRecipients(): void
    {
        $this->entityCollectionSetTest('recipient', 'School');
    }

    protected function getObject(): Alert
    {
        return $this->object;
    }
}
