<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\IngestionException;
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
     * @covers \Ilios\CoreBundle\Entity\IngestionException::setUser
     * @covers \Ilios\CoreBundle\Entity\IngestionException::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IngestionException::setUid
     * @covers \Ilios\CoreBundle\Entity\IngestionException::getUid
     */
    public function testSetTitle()
    {
        $this->basicSetTest('uid', 'string');
    }
}
