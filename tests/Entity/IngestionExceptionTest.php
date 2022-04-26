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
    /**
     * @var IngestionException
     */
    protected $object;

    /**
     * Instantiate a IngestionException object
     */
    protected function setUp(): void
    {
        $this->object = new IngestionException();
    }

    public function testNotBlankValidation()
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
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\IngestionException::setUid
     * @covers \App\Entity\IngestionException::getUid
     */
    public function testSetTitle()
    {
        $this->basicSetTest('uid', 'string');
    }
}
