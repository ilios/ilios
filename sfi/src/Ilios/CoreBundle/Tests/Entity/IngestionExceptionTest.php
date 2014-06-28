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
     * @covers Ilios\CoreBundle\Entity\IngestionException::setUserId
     */
    public function testSetUserId()
    {
        $this->basicSetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IngestionException::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IngestionException::setUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IngestionException::getUser
     */
    public function testGetUser()
    {
        $this->entityGetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IngestionException::setIngestedWideUid
     */
    public function testSetIngestedWideUid()
    {
        $this->basicSetTest('ingestedWideUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IngestionException::getIngestedWideUid
     */
    public function testGetIngestedWideUid()
    {
        $this->basicGetTest('ingestedWideUid', 'string');
    }
}
