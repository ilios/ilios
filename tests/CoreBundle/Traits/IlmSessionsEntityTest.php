<?php
namespace Tests\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\IlmSession;
use Ilios\CoreBundle\Traits\IlmSessionsEntity;
use Mockery as m;
use Tests\CoreBundle\TestCase;

/**
 * @coversDefaultClass \Ilios\CoreBundle\Traits\IlmSessionsEntity
 */

class IlmSessionsEntityTest extends TestCase
{
    /**
     * @var IlmSessionsEntity
     */
    private $traitObject;
    public function setUp()
    {
        $traitName = IlmSessionsEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers ::setIlmSessions
     */
    public function testSetIlmSessions()
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(IlmSession::class));
        $collection->add(m::mock(IlmSession::class));
        $collection->add(m::mock(IlmSession::class));

        $this->traitObject->setIlmSessions($collection);
        $this->assertEquals($collection, $this->traitObject->getIlmSessions());
    }

    /**
     * @covers ::addIlmSession
     */
    public function testAddIlmSessions()
    {
        $one = m::mock(IlmSession::class);
        $two = m::mock(IlmSession::class);

        $this->traitObject->setIlmSessions(new ArrayCollection());
        $this->traitObject->addIlmSession($one);
        $this->traitObject->addIlmSession($two);
        $this->assertEquals(2, $this->traitObject->getIlmSessions()->count());
        $this->assertEquals($one, $this->traitObject->getIlmSessions()->first());
        $this->assertEquals($two, $this->traitObject->getIlmSessions()->last());
    }

    /**
     * @covers ::removeIlmSession
     */
    public function testRemoveIlmSession()
    {
        $collection = new ArrayCollection();
        $one = m::mock(IlmSession::class);
        $two = m::mock(IlmSession::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setIlmSessions($collection);
        $this->traitObject->removeIlmSession($one);
        $ilmSessions = $this->traitObject->getIlmSessions();
        $this->assertEquals(1, $ilmSessions->count());
        $this->assertEquals($two, $ilmSessions->first());
    }
}
