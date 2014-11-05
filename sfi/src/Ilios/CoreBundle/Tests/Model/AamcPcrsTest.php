<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\AamcPcrs;
use Mockery as m;

/**
 * Tests for Model AamcPcrs
 */
class AamcPcrsTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\AamcPcrs::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCompetencies());
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\AamcPcrs::setPcrsId
     */
    public function testSetPcrsId()
    {
        $this->basicSetTest('pcrsId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcPcrs::getPcrsId
     */
    public function testGetPcrsId()
    {
        $this->basicGetTest('pcrsId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcPcrs::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcPcrs::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcPcrs::addCompetency
     */
    public function testAddCompetency()
    {
        $this->modelCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'AddCompetency');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcPcrs::removeCompetency
     */
    public function testRemoveCompetency()
    {
        $this->modelCollectionRemoveTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'AddCompetency',
            'removeCompetency'
        );
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcPcrs::getCompetencies
     */
    public function testGetCompetencies()
    {
        $this->modelCollectionGetTest('competencies', 'Competency', 'getCompetencies', false);
    }
}
