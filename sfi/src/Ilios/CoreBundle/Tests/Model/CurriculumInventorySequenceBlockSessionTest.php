<?php
namespace Ilios\CoreBundle\Tests\Model;

use Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSession;
use Mockery as m;

/**
 * Tests for Model CurriculumInventorySequenceBlockSession
 */
class CurriculumInventorySequenceBlockSessionTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSession::getSequenceBlockSessionId
     */
    public function testGetSequenceBlockSessionId()
    {
        $this->basicGetTest('sequenceBlockSessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSession::setCountOfferingsOnce
     */
    public function testSetCountOfferingsOnce()
    {
        $this->basicSetTest('countOfferingsOnce', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSession::getCountOfferingsOnce
     */
    public function testGetCountOfferingsOnce()
    {
        $this->basicGetTest('countOfferingsOnce', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSession::setSequenceBlock
     */
    public function testSetSequenceBlock()
    {
        $this->modelSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSession::getSequenceBlock
     */
    public function testGetSequenceBlock()
    {
        $this->modelGetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSession::setSession
     */
    public function testSetSession()
    {
        $this->modelSetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSession::getSession
     */
    public function testGetSession()
    {
        $this->modelGetTest('session', 'Session');
    }
}
