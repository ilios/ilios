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
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::setDescription
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::getDescription
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'AddCompetency');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcPcrs::getCompetencies
     */
    public function testGetCompetencies()
    {
        $this->entityCollectionSetTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'setCompetencies'
        );
    }
}
