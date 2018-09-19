<?php
namespace App\Tests\Entity;

use App\Entity\IngestionException;
use Mockery as m;

/**
 * Tests for Entity IngestionException
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
    protected function setUp()
    {
        $this->object = new IngestionException;
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
