<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AamcResourceType;

/**
 * Tests for Entity AamcResourceType
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\AamcResourceType::class)]
class AamcResourceTypeTest extends EntityBase
{
    protected AamcResourceType $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new AamcResourceType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'id',
            'title',
            'description',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('foo');
        $this->object->setDescription('bar');
        $this->object->setId('baz');
        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getTerms());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term', false, false, 'addAamcResourceType');
    }

    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term', false, false, false, 'removeAamcResourceType');
    }

    public function testGetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'Term', false, false, 'addAamcResourceType');
    }

    protected function getObject(): AamcResourceType
    {
        return $this->object;
    }
}
