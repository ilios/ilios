<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\AamcPcrs;
use Mockery as m;

/**
 * Tests for Entity AamcPcrs
 */
class AamcPcrsTest extends EntityBase
{
    /**
     * @var AamcPcrs
     */
    protected $object;

    /**
     * Instantiate a AamcPcrs object
     */
    protected function setUp()
    {
        $this->object = new AamcPcrs;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCompetencies());
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::setPcrsId
     */
    public function testSetPcrsId()
    {
        $this->basicSetTest('pcrsId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::getPcrsId
     */
    public function testGetPcrsId()
    {
        $this->basicGetTest('pcrsId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'AddCompetency');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::removeCompetency
     */
    public function testRemoveCompetency()
    {
        $this->entityCollectionRemoveTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'AddCompetency',
            'removeCompetency'
        );
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::getCompetencies
     */
    public function testGetCompetencies()
    {
        $this->entityCollectionGetTest('competencies', 'Competency', 'getCompetencies', false);
    }
}
