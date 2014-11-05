<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\IngestionException;
use Mockery as m;

/**
 * Tests for Model IngestionException
 */
class IngestionExceptionTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\IngestionException::setUserId
     */
    public function testSetUserId()
    {
        $this->basicSetTest('userId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IngestionException::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IngestionException::setUser
     */
    public function testSetUser()
    {
        $this->modelSetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IngestionException::getUser
     */
    public function testGetUser()
    {
        $this->modelGetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IngestionException::setIngestedWideUid
     */
    public function testSetIngestedWideUid()
    {
        $this->basicSetTest('ingestedWideUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\IngestionException::getIngestedWideUid
     */
    public function testGetIngestedWideUid()
    {
        $this->basicGetTest('ingestedWideUid', 'string');
    }
}
