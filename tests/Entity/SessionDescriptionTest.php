<?php
namespace App\Tests\Entity;

use App\Entity\SessionDescription;
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
     * @covers \App\Entity\SessionDescription::setDescription
     * @covers \App\Entity\SessionDescription::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\SessionDescription::setSession
     * @covers \App\Entity\SessionDescription::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }
}
