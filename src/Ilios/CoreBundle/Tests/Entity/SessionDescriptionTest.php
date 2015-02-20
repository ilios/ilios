<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\SessionDescription;
use Mockery as m;

/**
 * Tests for Entity SessionDescription
 */
class SessionDescriptionTest extends EntityBase
{
    /**
     * @var SessionDescription
     */
    protected $object;

    /**
     * Instantiate a SessionDescription object
     */
    protected function setUp()
    {
        $this->object = new SessionDescription;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionDescription::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionDescription::setSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }
}
