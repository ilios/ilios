<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\IngestionException;

/**
 * Tests for Entity IngestionException
 * @group model
 */
class IngestionExceptionTest extends EntityBase
{
    protected IngestionException $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new IngestionException();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'uid',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setUid('jayden_rules');
        $this->validate(0);
    }

    // not sure about this one -- there is the ID field which is NotBlank() but I recall this failing

    /**
     * @covers \App\Entity\IngestionException::setUser
     * @covers \App\Entity\IngestionException::getUser
     */
    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\IngestionException::setUid
     * @covers \App\Entity\IngestionException::getUid
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('uid', 'string');
    }

    protected function getObject(): IngestionException
    {
        return $this->object;
    }
}
