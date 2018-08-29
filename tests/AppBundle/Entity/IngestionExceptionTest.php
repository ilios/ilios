<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\IngestionException;
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
     * @covers \AppBundle\Entity\IngestionException::setUser
     * @covers \AppBundle\Entity\IngestionException::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \AppBundle\Entity\IngestionException::setUid
     * @covers \AppBundle\Entity\IngestionException::getUid
     */
    public function testSetTitle()
    {
        $this->basicSetTest('uid', 'string');
    }
}
