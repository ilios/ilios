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
     * @covers Ilios\CoreBundle\Entity\SessionDescription::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionDescription::setSession
     * @covers Ilios\CoreBundle\Entity\SessionDescription::getSession
     */
    public function testSetSession()
    {
        $this->softDeleteEntitySetTest('session', 'Session');
    }
}
