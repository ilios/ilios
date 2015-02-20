<?php
namespace Ilios\CoreBundle\Tests\Entity;

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

    /**
     * @covers Ilios\CoreBundle\Entity\IngestionException::setUser
     * @covers Ilios\CoreBundle\Entity\IngestionException::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }
}
