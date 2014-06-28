<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventorySequenceBlockSession
 */
class CurriculumInventorySequenceBlockSessionTest extends EntityBase
{
    /**
     * @var CurriculumInventorySequenceBlockSession
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventorySequenceBlockSession object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventorySequenceBlockSession;
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::getSequenceBlockSessionId
     */
    public function testGetSequenceBlockSessionId()
    {
        $this->basicGetTest('sequenceBlockSessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::setCountOfferingsOnce
     */
    public function testSetCountOfferingsOnce()
    {
        $this->basicSetTest('countOfferingsOnce', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::getCountOfferingsOnce
     */
    public function testGetCountOfferingsOnce()
    {
        $this->basicGetTest('countOfferingsOnce', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::setSequenceBlock
     */
    public function testSetSequenceBlock()
    {
        $this->entitySetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::getSequenceBlock
     */
    public function testGetSequenceBlock()
    {
        $this->entityGetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::setSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::getSession
     */
    public function testGetSession()
    {
        $this->entityGetTest('session', 'Session');
    }
}
