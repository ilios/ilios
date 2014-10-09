<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\ApiKey;
use Mockery as m;

/**
 * Tests for Model ApiKey
 */
class ApiKeyTest extends ModelBase
{
    /**
     * @var ApiKey
     */
    protected $object;

    /**
     * Instantiate a ApiKey object
     */
    protected function setUp()
    {
        $this->object = new ApiKey;
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\ApiKey::setUserId
     */
    public function testSetUserId()
    {
        $this->basicSetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ApiKey::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ApiKey::setApiKey
     */
    public function testSetApiKey()
    {
        $this->basicSetTest('apiKey', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ApiKey::getApiKey
     */
    public function testGetApiKey()
    {
        $this->basicGetTest('apiKey', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ApiKey::setUser
     */
    public function testSetUser()
    {
        $this->modelSetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ApiKey::getUser
     */
    public function testGetUser()
    {
        $this->modelGetTest('user', 'User');
    }
}
