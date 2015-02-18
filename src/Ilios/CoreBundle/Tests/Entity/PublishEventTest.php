<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\PublishEvent;
use Mockery as m;

/**
 * Tests for Entity PublishEvent
 */
class PublishEventTest extends EntityBase
{
    /**
     * @var PublishEvent
     */
    protected $object;

    /**
     * Instantiate a PublishEvent object
     */
    protected function setUp()
    {
        $this->object = new PublishEvent;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::setAdministrator
     */
    public function testSetAdministrator()
    {
        $this->entitySetTest('administrator', 'User');
    }
}
