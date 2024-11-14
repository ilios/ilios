<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AamcResourceType;

/**
 * Tests for Entity AamcResourceType
 * @group model
 */
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

    /**
     * @covers \App\Entity\AamcResourceType::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getTerms());
    }

    /**
     * @covers \App\Entity\AamcResourceType::setTitle
     * @covers \App\Entity\AamcResourceType::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\AamcResourceType::setDescription
     * @covers \App\Entity\AamcResourceType::getDescription
     */
    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\AamcResourceType::addTerm
     */
    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term', false, false, 'addAamcResourceType');
    }

    /**
     * @covers \App\Entity\AamcResourceType::removeTerm
     */
    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term', false, false, false, 'removeAamcResourceType');
    }

    /**
     * @covers \App\Entity\AamcResourceType::getTerms
     */
    public function testGetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'Term', false, false, 'addAamcResourceType');
    }

    protected function getObject(): AamcResourceType
    {
        return $this->object;
    }
}
