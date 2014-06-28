<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\ApiKey;
use Mockery as m;

/**
 * Tests for Entity ApiKey
 */
class ApiKeyTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\ApiKey::setUserId
     */
    public function testSetUserId()
    {
        $this->basicSetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ApiKey::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ApiKey::setApiKey
     */
    public function testSetApiKey()
    {
        $this->basicSetTest('apiKey', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ApiKey::getApiKey
     */
    public function testGetApiKey()
    {
        $this->basicGetTest('apiKey', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ApiKey::setUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ApiKey::getUser
     */
    public function testGetUser()
    {
        $this->entityGetTest('user', 'User');
    }
}
