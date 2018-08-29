<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\SessionDescription;
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
     * @covers \AppBundle\Entity\SessionDescription::setDescription
     * @covers \AppBundle\Entity\SessionDescription::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \AppBundle\Entity\SessionDescription::setSession
     * @covers \AppBundle\Entity\SessionDescription::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }
}
