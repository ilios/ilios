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
     * @covers Ilios\CoreBundle\Entity\ApiKey::setKey
     * @covers Ilios\CoreBundle\Entity\ApiKey::getKey
     */
    public function testSetKey()
    {
        $this->basicSetTest('key', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ApiKey::setUser
     * @covers Ilios\CoreBundle\Entity\ApiKey::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }
}
