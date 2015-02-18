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
     */
    public function testSetKey()
    {
        $this->basicSetTest('key', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ApiKey::setUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }
}
