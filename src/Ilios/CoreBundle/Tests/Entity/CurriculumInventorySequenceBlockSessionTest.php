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
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::setCountOfferingsOnce
     */
    public function testSetCountOfferingsOnce()
    {
        $this->booleanSetTest('countOfferingsOnce', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::setSequenceBlock
     */
    public function testSetSequenceBlock()
    {
        $this->entitySetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession::setSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }
}
